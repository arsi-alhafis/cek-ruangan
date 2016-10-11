<?php
 
require 'vendor/autoload.php';
$app = new \Slim\Slim();

function getDB()
{
    $dbhost = "localhost";
    $dbuser = "application";
    $dbpass = "application";
    $dbname = "jadwal";
 
    $mysql_conn_string = "mysql:host=$dbhost;dbname=$dbname";
    $dbConnection = new PDO($mysql_conn_string, $dbuser, $dbpass); 
    $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbConnection;
};

$app->get('/', function() use($app) {
    $app->response->setStatus(200);
    echo "Welcome to Slim based API";
});

$app->get('/ruangan/all', function () use ($app) {
    try 
    {
        $db = getDB();
 
        $sth = $db->prepare("SELECT nama FROM ruangan");
 
        $sth->bindParam(':gedung', $gedung, PDO::PARAM_INT);
        $sth->execute();
        $result = $sth->fetchAll();

        $data = [];
        for ($i = 0; $i < count($result); $i++) {
            $data[$i] = $result[$i][0];
        }

        $app->response->setStatus(200);
        $app->response()->headers->set('Content-Type', 'application/json');
        $response['status'] = 'SUCCESS';
        $response['data'] = $data;
        echo json_encode($response);
        $db = null;
 
    } catch(PDOException $e) {
        $app->response()->setStatus(404);
        echo json_encode(array("status" => "ERROR", "code" => 0, "message" => $e->getMessage()));
    }
});

$app->get('/lantai/:gedung', function ($gedung) use ($app) {
    try 
    {
        $db = getDB();
 
        $sth = $db->prepare("SELECT DISTINCT lantai FROM ruangan WHERE gedung = :gedung");
 
        $sth->bindParam(':gedung', $gedung, PDO::PARAM_INT);
        $sth->execute();
        $result = $sth->fetchAll();

        $data = [];
        for ($i = 0; $i < count($result); $i++) {
            $data[$i] = $result[$i][0];
        }

        $app->response->setStatus(200);
        $app->response()->headers->set('Content-Type', 'application/json');
        $response['status'] = 'SUCCESS';
        $response['data'] = $data;
        echo json_encode($response);
        $db = null;
 
    } catch(PDOException $e) {
        $app->response()->setStatus(404);
        echo json_encode(array("status" => "ERROR", "code" => 0, "message" => $e->getMessage()));
    }
});

$app->get('/ruangan/:gedung/:lantai', function ($gedung, $lantai) use ($app) {
    try 
    {
        $db = getDB();
 
        $sth = $db->prepare("SELECT DISTINCT nama FROM ruangan WHERE gedung = :gedung AND lantai = :lantai");
 
        $sth->bindParam(':gedung', $gedung, PDO::PARAM_INT);
        $sth->bindParam(':lantai', $lantai, PDO::PARAM_INT);
        $sth->execute();
        $result = $sth->fetchAll();

        $data = [];
        for ($i = 0; $i < count($result); $i++) {
            $data[$i] = $result[$i][0];
        }

        $app->response->setStatus(200);
        $app->response()->headers->set('Content-Type', 'application/json');
        $response['status'] = 'SUCCESS';
        $response['data'] = $data;
        echo json_encode($response);
        $db = null;
 
    } catch(PDOException $e) {
        $app->response()->setStatus(404);
        echo json_encode(array("status" => "ERROR", "code" => 0, "message" => $e->getMessage()));
    }
});

$app->get('/jadwal/:hari/:ruangan', function ($hari, $ruangan) use ($app) {
    try 
    {
        $db = getDB();
 
        $sth = $db->prepare("SELECT j.hari, j.jamMulai, j.jamSelesai, m.nama as matkul, r.nama as ruangan FROM jadwal j, ruangan r, matkul m WHERE r.nama = :ruangan and j.hari = :hari and j.matkul = m.id and r.id = j.ruangan ORDER BY j.jamMulai");
 
        $sth->bindParam(':hari', $hari, PDO::PARAM_INT);
        $sth->bindParam(':ruangan', $ruangan, PDO::PARAM_INT);
        $sth->execute();
        $result = $sth->fetchAll();

        $data = [];
        for ($i = 0; $i < count($result); $i++) {
            $matkul = $result[$i][3];
            $jamMulai = $result[$i][1];
            $jamSelesai = $result[$i][2];
            
            $jadwal = array('matkul' => $matkul, 'jam_mulai' => $jamMulai, 'jam_selesai' => $jamSelesai);
            $data[$i] = $jadwal;
        }

        $app->response->setStatus(200);
        $app->response()->headers->set('Content-Type', 'application/json');
        $response['status'] = 'SUCCESS';
        $response['data'] = $data;
        echo json_encode($response);
        $db = null;
 
    } catch(PDOException $e) {
        $app->response()->setStatus(404);
        echo json_encode(array("status" => "ERROR", "code" => 0, "message" => $e->getMessage()));
    }
});
 
$app->run();

?>