<?php
    //init
function __autoload($class_name) {
    $directorys = array(
        '../dble/',
        '../dble/mysql/',
        '../dble/access-accdb/',
        '../dble/access-mdb/'
    );
    
    foreach($directorys as $directory) {
        if(file_exists(dirname(__FILE__)."/" . $directory .$class_name.".php")) {
            require_once(dirname(__FILE__)."/" . $directory .$class_name.".php");
            return;
        }
    }
}

$dble = new MySQLDBLE("localhost",3306,"root","usbw","testdb");

//Creez scheletul obiectului
$skel = $dble->createObject("persoane", "Persoana");
$skel->addLink("private", "id", true);
$skel->addLink("public", "nume", false);
$skel->addLink("private", "prenume", false);
$skel->addLink("private", "varsta", false);
$skel->addLink("private", "cnp", false);
?>
<!doctype html>
<html>
    <head>
        <title>Exemplu DBLE</title>
        <link rel="stylesheet" href="./style.css" media="all" />
        
    </head>
    <body>
        <div id="wrapper">
            <h2 id="titlu">PHP Database Linking Engine DoCPoCPoC</h2>
            <table border="0" width="100%">
                <tr>
                    <td width="50%" valign="top" align="left">
                        <?php
                            if (isset($_POST["butonadaugare"])) {
                                if (strlen($_POST["nume"]) == 0) {
                                    echo "<font color=\"red\">Trebuie sa completati numele!</font>";
                                } else {
                                    $nume = $_POST["nume"];
                                    $prenume = $_POST["prenume"];
                                    $varsta = (int)$_POST["varsta"];
                                    $cnp = $_POST["cnp"];
                                    //Instantiere pornind de la date
                                    $obiect = $skel->instantiateFromData(array($nume,$prenume,$varsta,$cnp));
                                    //Salvare in baza
                                    $obiect->toDB();
                                    echo "<font color=\"green\">S-a adaugat in baza (id=".$obiect->getId()."): $nume, $prenume, $varsta, $cnp</font>";
                                }
                            }
                        ?>
                        <form id="formpersoane" method="post" action="">
                            <table border="0">
                                <tr><td colspan="2"><b>Adaugare persoane</b></td></tr>
                                <tr><td>Nume*:</td><td><input type="text" name="nume" maxlength="100" /></td></tr>
                                <tr><td>Prenume:</td><td><input type="text" name="prenume" maxlength="100" /></td></tr>
                                <tr><td>Varsta:</td><td><input type="text" name="varsta" maxlength="3" /></td></tr>
                                <tr><td>CNP:</td><td><input type="text" name="cnp" maxlength="13" /></td></tr>
                                <tr><td colspan="2" align="center"><input type="submit" value="Adauga" name="butonadaugare" /></td></tr>
                            </table>
                        </form>
                    </td>
                    <td width="50%" valign="top" align="left">
                        <form id="formafisare" method="post" action="">
                            <table border="0">
                                <tr><td>ID Persoana: </td><td><input name="id" type="text" maxlength="4" /></td><td><input type="submit" name="butonafisare" value="Afiseaza informatii" /></td></tr>
                            </table>
                        </form>
                        
                        <!-- LISTA DATE -->
                        <table border="1" id="date">
                            <tr><td>ID</td><td>Nume</td><td>Prenume</td><td>Varsta</td><td>CNP</td></tr>
                        <?php
                            if (isset($_POST["butonafisare"])) {
                                if (strlen($_POST["id"]) == 0) {
                                    echo "<font color=\"red\">Trebuie sa completati ID-ul!</font>";
                                } else {
                                    $id = (int)$_POST["id"];
                                    
                                    //Instantiem dupa PK
                                    $obiect2 = $skel->instantiateFromPK($id);
                                    echo "<tr>";
                                    echo "<td>".$obiect2->getId()."</td>";
                                    echo "<td>".$obiect2->nume."</td>"; //public
                                    echo "<td>".$obiect2->getPrenume()."</td>";
                                    echo "<td>".$obiect2->getVarsta()."</td>";
                                    echo "<td>".$obiect2->getCnp()."</td>";
                                    echo "</tr>";
                                }
                            }
                        ?>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>