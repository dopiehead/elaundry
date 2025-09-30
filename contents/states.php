<select name="user_location" class="form-select locationFilter">
     <option value="">Entire Nigeria</option>
       <?php

        require("../engine/connection.php");

        $getstates = $con->prepare("SELECT DISTINCT state FROM states_in_nigeria ORDER BY state ASC");
        $getstates->execute();
        $result = $getstates->get_result();

        while ($row = $result->fetch_assoc()) {
       $st = $row['state']; ?>
       <option value='<?= htmlspecialchars($st) ?>'><?= htmlspecialchars($st) ?></option>
      <?php } ?>
</select>