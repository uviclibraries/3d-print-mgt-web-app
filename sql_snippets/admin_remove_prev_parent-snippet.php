<?php

if($prev_parent_id != 0){
    echo($prev_parent_id);
    $stm = $conn->prepare("SELECT COUNT(*) FROM web_job WHERE parent_job_id = :parent_job_id");
    $stm->bindParam(':parent_job_id', $prev_parent_id);
    $stm->execute();
    print_r($stm);
    $num_children = $stm->fetchColumn();
    echo($num_children);

    if($num_children == 0){
        $is_parent = false;
        $set_isParent = $conn->prepare("UPDATE web_job SET is_parent = :new_is_parent WHERE id = :prev_parent");
        $set_isParent->bindParam(':new_is_parent', $is_parent, PDO:: PARAM_BOOL);
        $set_isParent->bindParam(':prev_parent',$prev_parent_id);
        $set_isParent->execute();
    }
}
?>
