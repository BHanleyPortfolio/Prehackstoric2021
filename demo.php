<?php
if(isset($_GET['year'])){
    $birthYear = $_GET['year'];
    $birthMonth = $_GET['month'];
    $birthDay = $_GET['day'];

    $inputBirthdate = $birthYear.'-'.$birthMonth.'-'.$birthDay;
}

function keepDefault($i,$size){
    if(isset($_GET[$size])){
        if($_GET[$size] == $i){
            return ' selected';
        }
    }
}
?>
<form name="open_file" action="" method="get">
<select name="year">
    <?php
        for($i=2021;$i>=1910;$i--){
            echo "<option value=\"{$i}\"".keepDefault($i,'year').">{$i}</option>";
        }
    ?>
</select>
    <select name="month">
        <?php
        for($i=1;$i<=12;$i++){
            echo "<option value=\"{$i}\"".keepDefault($i,'month').">{$i}</option>";
        }
        ?>
    </select>
    <select name="day">
        <?php
        for($i=1;$i<=31;$i++){
            echo "<option value=\"{$i}\"".keepDefault($i,'day').">{$i}</option>";
        }
        ?>
    </select>
<button type="submit" name="submit">Get Dino</button>
</form>

<?php
if(isset($_GET['year'])) {
    $currentDate = date("Y-m-d");
    $date = new DateTime($currentDate);//date("Y/m/d");
    $birthdate = new DateTime($inputBirthdate);
    $age = $birthdate->diff($date);

    echo "You are " . $age->y . " years, " . $age->m . " months, and " . $age->d . " days old. ";
    //print "Today is " . $currentDate;

    try {
        $dbh = new PDO('pgsql:host=localhost;port=26257;dbname=dinodb;sslmode=require;sslrootcert=certs/ca.crt;sslkey=certs/client.dinodbuser.key;sslcert=certs/client.dinodbuser.crt',
            'dinodbuser', null, array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => true,
                PDO::ATTR_PERSISTENT => true
            ));

        //$dbh->exec('INSERT INTO accounts (id, balance) VALUES (1, 1000), (2, 250)');

        print "Dinosaurs: <br>";
        foreach ($dbh->query('SELECT dinosaur, lifespan FROM dinoinfo WHERE lifespan < ' . $age->y . ' ORDER BY RANDOM() LIMIT 1') as $row) {
            print ucwords($row['dinosaur']) . ': ' . $row['lifespan'] . " years<br><img src=\"images/".trim($row['dinosaur']).".jpg\" width=\"80%\" />";
        }
    } catch (Exception $e) {
        print $e->getMessage() . "\r\n";
        exit(1);
    }
}
?>
