<select name="user_location" class="form-select location locationFilter text-capitalize">
    <option value="">Entire Nigeria</option>
    <?php
    require_once __DIR__ . '/../engine/connection.php';

    $stmt = $con->prepare("SELECT DISTINCT state FROM states_in_nigeria ORDER BY state ASC");
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()):
        $state = htmlspecialchars($row['state'], ENT_QUOTES, 'UTF-8');
    ?>
        <option value="<?= htmlspecialchars($state) ?>"><?=  htmlspecialchars($state) ?></option>
    <?php endwhile; ?>
</select>
