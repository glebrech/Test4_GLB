<?php
use PHPUnit\Framework\TestCase;

require_once "Constantes.php";
include_once "PDO/connectionPDO.php";
require_once "metier/Adresse.php";
require_once "PDO/AdresseDB.php";

,
class AdresseDBTest extends TestCase
{
    @var AdresseDB
    protected $adresse;
    protected $pdodb;

    protected function setUp():void 
    {
        $strConnection = Constantes::TYPE . ":host=" . Constantes::HOST . ";dbname=" . Constantes::BASE;
        $arrExtraParam = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
        $this->pdodb = new PDO($strConnection, Constantes::USER, Constantes::PASSWORD, $arrExtraParam); 
        $this->pdodb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }


protected function tearDown(): void
    {
    }
    public function testAjout()
    {
        try {
            $this->adresse = new AdresseDB($this->pdodb);

            $p = new Adresse(4, "Rue d'Alsace", 78000, "Sartrouvile", 4);
            $this->adresse->ajout($p);

            $adr = $this->adresse->selectAdresse($this->pdodb->lastInsertId());
            $this->assertEquals($p->getNumero(), $adr->getNumero());
            $this->assertEquals($p->getRue(), $adr->getRue());
            $this->assertEquals($p->getCodepostal(), $adr->getCodepostal());
            $this->assertEquals($p->getVille(), $adr->getVille());
            $this->assertEquals($p->getId(), $adr->getId());
        } catch (Exception $e) {
            echo 'Exception reçue : ',  $e->getMessage(), "\n";
        }
    }

    public function testdeSuppression()
    {
        try {
            $this->adresse = new AdresseDB($this->pdodb);

            $adr = $this->adresse->selectAdresse(1);
            $this->adresse->suppression($adr);
            $adr2 = $this->adresse->selectAdresse(1);
            if ($adr2 != null) {
                $this->TestIncomplet(
                    "Echec de la suppression de l'enregistrement adresse"
                );
            }
        } catch (Exception $e) {
            //verification exception
            $exception = "RECORD ADRESSE not is in DATABASE";
            $this->assertEquals($exception, $e->getMessage());
        }
    }
    public function testdeSelectionAdresse()
    {
        $this->adresse = new AdresseDB($this->pdodb);
        $p = new Adresse(4, "Rue d'Alsace", 78000, "Sartrouville", 4);
        $this->adresse->ajout($p);

        $adr = $this->adresse->selectAdresse($this->pdodb->lastInsertId());
        $this->assertEquals($p->getNumero(), $adr->getNumero());
        $this->assertEquals($p->getRue(), $adr->getRue());
        $this->assertEquals($p->getCodepostal(), $adr->getCodepostal());
        $this->assertEquals($p->getVille(), $adr->getVille());
        $this->assertEquals($p->getId(), $adr->getId());
    }

    PUBLIC FUNCTION   testSelecttout()
    {
        $ok = true;
        $this->adresse=new AdresseDB($this->pdo);
        $res = $this->adresse->sectAll();
        $i=0;
        foreach($res as $key => $values) {
            $i++;

        }
        if ($i ==0) {
            $this->TestIncomplet("Pas de résultat");
            $ok=false;
        }
        $this->assertTrue($ok);
    }

    public function testConvertePDOPers()
    {
        $tab['id']=6;
        $tab['numero'] = 4;
        $tab["rue"] = "Rue d'Alsace";
        $tab["codepostal"] = 78000;
        $tab["ville"] = "Sartrouville";
        $tab["id_pers"] = 4;
        $this->adresse = new AdresseDB($this->pdodb);
        $adr = $this->adresse->convertPdoAdr($tab);
        $this->assertEquals($tab["id"], $adr->getId());
        $this->assertEquals($tab["numero"], $adr->getNumero());
        $this->assertEquals($tab["rue"], $adr->getRue());
        $this->assertEquals($tab["codepostal"], $adr->getCodepostal());
        $this->assertEquals($tab["ville"], $adr->getVille());
        $this->assertEquals($tab["persId"], $adr->getPersId());
    }

    public function testUpdate()
    {

        $this->adresse = new AdresseDB($this->pdodb);
        $p = new Adresse(4, "Rue d'Alsace", 78000, "Sartrouville", 4);
        $this->adresse->ajout($p);
        $lastId = $this->pdodb->lastInsertId();
        $p->setId($lastId);
        $this->adresse->update($p);
        $adr = $this->adresse->selectAdresse($p->getId());
        print_r(array($p,$adr));
        $this->assertEquals($p->getNumero(), $adr->getNumero());
        $this->assertEquals($p->getRue(), $adr->getRue());
        $this->assertEquals($p->getCodepostal(), $adr->getCodepostal());
        $this->assertEquals($p->getVille(), $adr->getVille());
        $this->assertEquals($p->getPersId(), $adr->getPersId());
        $this->assertEquals($p->getId(), $adr->getId());
    }
}

