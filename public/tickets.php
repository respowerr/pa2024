<?php 
session_start();
if (!isset($_SESSION['accessToken'])) {
    header("Location: login.php");
    exit;
}
$baseUrl = "http://ddns.callidos-mtf.fr:8080/tickets";
$myTicketsUrl = $baseUrl . "/mytickets";
$authHeader = "Authorization: Bearer " . $_SESSION['accessToken'];
function makeHttpRequest($url, $method, $data = null) {
    $options = [
        "http" => [
            "header" => "Content-Type: application/json\r\n" . $GLOBALS['authHeader'] . "\r\n",
            "method" => $method
        ]
    ];
    if ($data !== null) {
        $options['http']['content'] = json_encode($data);
    }
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return $result === FALSE ? [] : json_decode($result, true);
}
$response = null;
$messages = [];
$ticketId = $_GET['ticket_id'] ?? null;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create_ticket'])) {
        $data = ["title" => $_POST['title'], "desc" => $_POST['desc']];
        $response = makeHttpRequest($baseUrl, "POST", $data);
    } elseif (isset($_POST['send_message'])) {
        $data = ["message" => $_POST['message']];
        $response = makeHttpRequest($baseUrl . "/$ticketId/messages", "POST", $data);
    }
}
if ($ticketId) {
    $messages = makeHttpRequest($baseUrl . "/$ticketId/messages", "GET");
}
$myTickets = $ticketId ? [] : makeHttpRequest($myTicketsUrl, "GET");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/head.php');
    ?>
    <?php if ($ticketId): ?>
    <script>
        setTimeout(function() {
            window.location.reload();
        }, 5000);
    </script>
    <?php endif; ?>
</head>
<body>
    <div class="wrapper">
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php'); ?>
        <main>
            <div class="content">
                <div class="container">
                    <h1 class="title">Ticket Management</h1>
                    <?php if (!$ticketId): ?>
                        <form method="post">
                            <div class="field">
                                <label class="label">Title</label>
                                <div class="control">
                                    <input class="input" type="text" name="title" required>
                                </div>
                            </div>
                            <div class="field">
                                <label class="label">Description</label>
                                <div class="control">
                                    <textarea class="textarea" name="desc" required></textarea>
                                </div>
                            </div>
                            <div class="control">
                                <button type="submit" name="create_ticket" class="button is-link">Create Ticket</button>
                            </div>
                        </form>
                        <div class="section">
                            <table class="table is-striped is-fullwidth">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($myTickets as $ticket): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($ticket['ticket_id']); ?></td>
                                            <td><?= htmlspecialchars($ticket['title']); ?></td>
                                            <td><?= $ticket['resolved'] ? 'Resolved' : 'Not Resolved'; ?></td>
                                            <td><a href="?ticket_id=<?= $ticket['ticket_id'] ?>" class="button is-small is-info">Open Chat</a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <a href="tickets.php" class="button is-small is-primary">Return to Tickets</a>
                        <div class="box">
                            <h2 class="title is-4">Messages for Ticket #<?= htmlspecialchars($ticketId); ?></h2>
                            <?php foreach ($messages as $message): ?>
                                <div>
                                    <span><?= htmlspecialchars($message['sender']); ?>:</span>
                                    <span><?= htmlspecialchars($message['date']); ?></span>
                                    <p><?= htmlspecialchars($message['message']); ?></p>
                                </div>
                            <?php endforeach; ?>
                            <form method="post">
                                <input type="hidden" name="ticket_id" value="<?= htmlspecialchars($ticketId); ?>">
                                <textarea class="textarea" name="message" placeholder="Type your message here..." required></textarea>
                                <button type="submit" name="send_message" class="button is-link">Send Message</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'); ?>
    </div>
</body>
</html>
