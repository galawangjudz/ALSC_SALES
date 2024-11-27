<?php

try {
    $query =$conn->query("SELECT c_lid, c.c_acronym, h.c_house_lid, h.c_house_model, h.c_floor_area, 
    h.c_h_price_sqm , i.c_block, i.c_lot, i.c_status, i.c_lot_area, i.c_price_sqm 
    FROM t_lots i 
    LEFT JOIN t_projects c 
    ON i.c_site = c.c_code
    LEFT JOIN t_house h  
    ON i.c_house_lid = h.c_house_lid
    WHERE i.c_site = c.c_code  and  (i.c_status = 'Available' )
    ORDER BY c.c_acronym, i.c_block, i.c_lot");


    $projects = $query->fetch_assoc();

    echo json_encode(['success' => true, 'project_map' => $projects]);

} catch (PDOException $e) {
    // Handle errors
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>