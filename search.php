<?php
    //database configuration
    $dbHost = 'localhost';
    $dbUsername = 'application';
    $dbPassword = 'application';
    $dbName = 'jadwal';
    
    //connect with the database
    $db = new mysqli($dbHost,$dbUsername,$dbPassword,$dbName);
    
    //get search term
    $searchTerm = $_GET['term'];
    
    //get matched data from skills table
    $query = $db->query("SELECT nama FROM ruangan WHERE nama LIKE '%".$searchTerm."%' ORDER BY nama ASC");
    while ($row = $query->fetch_assoc()) {
        $data[] = $row['nama'];
    }
    
    //return json data
    echo json_encode($data);
?>