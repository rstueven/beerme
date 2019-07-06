<?php $documentTitle = "The most complete source of brewery information worldwide." ?>
<?php include 'layouts/top.php'; ?>

<h1>BEER ME!</h1>

<!--div id="map" style="width: 100vw; height: 100vh;"></div-->

<ul>
<?php
$res = $mysqli->query("SELECT name FROM brewery ORDER BY name");
if ($res) {
    $res->data_seek(0);
    while ($row = $res->fetch_assoc()) {
        echo "<li>" . $row['name'] . "</li>\n";
    }
    $res->close();
} else {
    echo "<li>query failed: " . $mysqli->error . "</li>";
}
?>
</ul>

<?php include 'layouts/bottom.php'; ?>