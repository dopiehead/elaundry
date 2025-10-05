<select name="user_location" class="form-select location locationFilter text-capitalize">
    <option value="">Entire Nigeria</option>
    <?php
    require("../engine/connection.php");

    $stmt = $con->prepare("SELECT DISTINCT state FROM states_in_nigeria ORDER BY state ASC");
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()):
        $state = htmlspecialchars($row['state'], ENT_QUOTES, 'UTF-8');
    ?>
        <option value="<?= $state ?>"><?= $state ?></option>
    <?php endwhile; ?>
</select>
