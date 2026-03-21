<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
</head>

<body>
    <?php require base_path("app/Views/partials/navbar.php") ?>

    <?= $content ?>

    <?php require base_path("app/Views/partials/footer.php") ?>
</body>

</html>