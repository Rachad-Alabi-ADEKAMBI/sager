import { Capacitor } from '@capacitor/core';
import { Directory, Filesystem } from '@capacitor/filesystem';
import { Share } from '@capacitor/share';
import { jsPDF } from 'jspdf';

function sanitizeFilename(value) {
  const fallback = `sager-${new Date().toISOString().slice(0, 10)}.pdf`;
  if (!value) return fallback;

  const filename = String(value)
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '')
    .slice(0, 80);

  return `${filename || fallback.replace('.pdf', '')}.pdf`;
}

function extractPrintableHtml(html) {
  const parser = new DOMParser();
  const doc = parser.parseFromString(html, 'text/html');
  const title =
    doc.querySelector('h1, h2, h3, h4')?.textContent?.trim() ||
    doc.title ||
    'SAGER';

  doc.querySelectorAll('script, .actions, .print-button, button').forEach((node) => {
    node.remove();
  });

  const styles = Array.from(doc.querySelectorAll('style'))
    .map((style) => style.textContent || '')
    .join('\n');

  return {
    title,
    styles,
    body: doc.body.innerHTML || html,
  };
}

function createRenderContainer({ styles, body }) {
  const container = document.createElement('div');
  container.style.position = 'fixed';
  container.style.left = '-10000px';
  container.style.top = '0';
  container.style.width = '794px';
  container.style.background = '#ffffff';
  container.style.color = '#111111';
  container.innerHTML = `
    <style>
      * { box-sizing: border-box; }
      body { margin: 0; }
      ${styles || ''}
      .actions, button { display: none !important; }
    </style>
    ${body}
  `;
  document.body.appendChild(container);
  return container;
}

function blobToBase64(blob) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onloadend = () => {
      const result = String(reader.result || '');
      resolve(result.includes(',') ? result.split(',')[1] : result);
    };
    reader.onerror = reject;
    reader.readAsDataURL(blob);
  });
}

async function saveOrSharePdf(doc, filename, title) {
  if (Capacitor.isNativePlatform()) {
    const blob = doc.output('blob');
    const data = await blobToBase64(blob);
    const writeResult = await Filesystem.writeFile({
      path: filename,
      data,
      directory: Directory.Cache,
      recursive: true,
    });

    try {
      await Share.share({
        title,
        text: title,
        url: writeResult.uri,
        dialogTitle: 'Partager le PDF',
      });
      return;
    } catch (error) {
      console.warn('[SagerPdf] Share failed, PDF saved in cache.', error);
    }
  }

  doc.save(filename);
}

export async function saveHtmlAsPdf(html, options = {}) {
  const printable = extractPrintableHtml(html);
  const title = options.title || printable.title || 'SAGER';
  const filename = sanitizeFilename(options.filename || title);
  const container = createRenderContainer(printable);

  try {
    const pdf = new jsPDF({
      unit: 'pt',
      format: 'a4',
      orientation: options.orientation || 'portrait',
    });

    await pdf.html(container, {
      x: 24,
      y: 24,
      width: 547,
      windowWidth: 794,
      html2canvas: {
        scale: 0.72,
        useCORS: true,
      },
      autoPaging: 'text',
    });

    await saveOrSharePdf(pdf, filename, title);
  } finally {
    container.remove();
  }
}

export async function saveElementAsPdf(element, options = {}) {
  const clone = element.cloneNode(true);
  clone.querySelectorAll('script, .actions, .print-button, button').forEach((node) => {
    node.remove();
  });

  const styles = Array.from(document.querySelectorAll('style'))
    .map((style) => style.textContent || '')
    .join('\n');

  await saveHtmlAsPdf(
    `<!doctype html><html><head><style>${styles}</style></head><body>${clone.outerHTML}</body></html>`,
    options,
  );
}

export function installPdfPrintAdapter() {
  if (window.__sagerPdfPrintInstalled) return;
  window.__sagerPdfPrintInstalled = true;

  const originalOpen = window.open.bind(window);

  window.SagerPdf = {
    saveHtmlAsPdf,
    saveElementAsPdf,
  };

  window.open = function patchedOpen(url = '', target, features) {
    if (url && url !== 'about:blank') {
      return originalOpen(url, target, features);
    }

    let html = '';
    let pdfGenerated = false;
    let autoPdfTimer = null;

    const generateOnce = () => {
      if (pdfGenerated) return Promise.resolve();
      pdfGenerated = true;
      if (autoPdfTimer) {
        clearTimeout(autoPdfTimer);
        autoPdfTimer = null;
      }
      return saveHtmlAsPdf(html || document.documentElement.outerHTML);
    };

    const fakeDocument = {
      open() {
        html = '';
      },
      write(content) {
        html += content || '';
      },
      close() {
        autoPdfTimer = setTimeout(() => {
          generateOnce();
        }, 500);
      },
    };

    return {
      document: fakeDocument,
      focus() {},
      close() {},
      print() {
        return generateOnce();
      },
    };
  };

  window.print = function patchedPrint() {
    const printable = document.querySelector('#invoice') || document.body;
    return saveElementAsPdf(printable, {
      title: document.querySelector('h4, h2, h1')?.textContent?.trim() || 'SAGER',
    });
  };
}
