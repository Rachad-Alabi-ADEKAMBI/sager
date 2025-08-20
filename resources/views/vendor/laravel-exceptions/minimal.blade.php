<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Error</title>
</head>

<body>
    <h1>Une erreur est survenue</h1>
    <p>{{ $exception->getMessage() ?? 'Erreur serveur' }}</p>
</body>

</html>
