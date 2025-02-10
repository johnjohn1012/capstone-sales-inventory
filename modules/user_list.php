<?php

// Database Connection
include "includes/connection.php";

$sql = "SELECT id, name, role, gender, email, password, profile_image FROM users";
$result = $con->query($sql);

?>
<br>
    <title>User List</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
        }
        .action-buttons button {
            margin-right: 10px;
            padding: 5px 10px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        .delete { background-color: red; color: white; }
        .update { background-color: blue; color: white; }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 30%;
            text-align: center;
        }
        .close {
            color: red;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2>User List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Role</th>
            <th>Gender</th>
            <th>Email</th>
         <!--th>Password</th> -->
            <th>Profile Image</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["name"] . "</td>";
                echo "<td>" . $row["role"] . "</td>";
                echo "<td>" . $row["gender"] . "</td>";
                echo "<td>" . $row["email"] . "</td>";
         
                echo "<td><img src='uploads/" . $row["profile_image"] . "' width='60' height='60'></td>";
                echo "<td class='action-buttons'>";
                echo "<button class='update' onclick='openModal(" . $row["id"] . ", \"update\")'>Update</button>";
                echo "<button class='delete' onclick='openModal(" . $row["id"] . ", \"delete\")'>Delete</button>";
                
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No users found</td></tr>";
        }
        ?>
    </table>

    <div id="actionModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <p id="modalText"></p>
            <button id="confirmAction">Confirm</button>
        </div>
    </div>

    <script>
        function openModal(userId, action) {
            let modal = document.getElementById("actionModal");
            let modalText = document.getElementById("modalText");
            let confirmButton = document.getElementById("confirmAction");

            modal.style.display = "block";
            modalText.innerHTML = "Are you sure you want to " + action + " this user?";
            confirmButton.onclick = function () {
                window.location.href = action + ".php?id=" + userId;
            };
        }
        
        function closeModal() {
            document.getElementById("actionModal").style.display = "none";
        }
    </script>

<?php
$con->close();
?>
