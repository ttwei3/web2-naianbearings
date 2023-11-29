<!-- manage_comments.php -->
<?php
require('connect.php');
require('header_admin.php');
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 

function disemvowel($comment) {
    return preg_replace('/[aeiouAEIOU]/', '', $comment);
}

$commentOrderBy = 'username'; // 默认排序字段
$commentOrderDirection = 'ASC'; // 默认排序方向

// Redirect non-admins back to index page
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: index.php');
    exit();
}

// 检查是否有新的排序请求
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $validSortFields = ['username', 'comment_date'];
    $validSortDirections = ['ASC', 'DESC'];

    $commentOrderBy = in_array($_POST['comment_sort_by'], $validSortFields) ? $_POST['comment_sort_by'] : 'username';
    $commentOrderDirection = in_array($_POST['comment_sort_dir'], $validSortDirections) ? $_POST['comment_sort_dir'] : 'ASC';
}

// 构建带排序的 SQL 查询
$commentQuery = "SELECT *, DATE_FORMAT(comment_date, '%Y-%m-%d %H:%i:%s') AS formatted_date, is_hidden FROM Comments ORDER BY $commentOrderBy $commentOrderDirection";
$commentStmt = $db->prepare($commentQuery);
$commentStmt->execute();
$comments = $commentStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all comments with sorting
$commentQuery = "SELECT *, DATE_FORMAT(comment_date, '%Y-%m-%d %H:%i:%s') AS formatted_date, is_hidden FROM Comments ORDER BY $commentOrderBy $commentOrderDirection";
$commentStmt = $db->prepare($commentQuery);
$commentStmt->execute();
$comments = $commentStmt->fetchAll(PDO::FETCH_ASSOC);

// Delete comment
if (isset($_POST['comment_id'])) {
    $deleteQuery = "DELETE FROM Comments WHERE comment_id = :comment_id";
    $deleteStmt = $db->prepare($deleteQuery);
    $deleteStmt->bindParam(':comment_id', $_POST['comment_id'], PDO::PARAM_INT);
    $deleteStmt->execute();

    // Redirect back to admin page
    header('Location: manage_comments.php');
    exit();
}

// 隐藏评论
if (isset($_POST['hide_comment_id'])) {
    $hideQuery = "UPDATE Comments SET is_hidden = TRUE WHERE comment_id = :comment_id";
    $hideStmt = $db->prepare($hideQuery);
    $hideStmt->bindParam(':comment_id', $_POST['hide_comment_id'], PDO::PARAM_INT);
    $hideStmt->execute();

    header('Location: manage_comments.php');
    exit();
}

// 应用 Disemvoweling
if (isset($_POST['disemvowel_comment_id'])) {
    $commentId = $_POST['disemvowel_comment_id'];

    // 获取评论
    $fetchQuery = "SELECT comment_content, original_comment FROM Comments WHERE comment_id = :comment_id";
    $fetchStmt = $db->prepare($fetchQuery);
    $fetchStmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
    $fetchStmt->execute();
    $commentData = $fetchStmt->fetch(PDO::FETCH_ASSOC);

    // 检查 original_comment 是否已经设置
    if (empty($commentData['original_comment'])) {
        $originalComment = $commentData['comment_content'];
    } else {
        $originalComment = $commentData['original_comment'];
    }

    // 应用 Disemvoweling
    $disemvoweledComment = disemvowel($commentData['comment_content']);

    // 更新评论内容，如果 original_comment 为空，则设置它
    $updateQuery = "UPDATE Comments SET comment_content = :new_content, original_comment = :original_comment, is_disemvoweled = TRUE WHERE comment_id = :comment_id";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->bindParam(':new_content', $disemvoweledComment);
    $updateStmt->bindParam(':original_comment', $originalComment);
    $updateStmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
    $updateStmt->execute();

    header('Location: manage_comments.php');
    exit();
}

// 恢复显示评论
if (isset($_POST['unhide_comment_id'])) {
    $unhideQuery = "UPDATE Comments SET is_hidden = FALSE WHERE comment_id = :comment_id";
    $unhideStmt = $db->prepare($unhideQuery);
    $unhideStmt->bindParam(':comment_id', $_POST['unhide_comment_id'], PDO::PARAM_INT);
    $unhideStmt->execute();

    header('Location: manage_comments.php');
    exit();
}

// 恢复原文
if (isset($_POST['restore_comment_id'])) {
    $restoreQuery = "UPDATE Comments SET comment_content = original_comment, is_disemvoweled = FALSE WHERE comment_id = :comment_id";
    $restoreStmt = $db->prepare($restoreQuery);
    $restoreStmt->bindParam(':comment_id', $_POST['restore_comment_id'], PDO::PARAM_INT);
    $restoreStmt->execute();

    header('Location: manage_comments.php');
    exit();
}
?>


    <h1>Manage Comments</h1>

    <form action="manage_comments.php" method="post">
        <label for="comment_sort_by">Sort comments by:</label>
        <select name="comment_sort_by" id="comment_sort_by">
            <option value="username" <?php echo $commentOrderBy == 'username' ? 'selected' : ''; ?>>Username</option>
            <option value="comment_date" <?php echo $commentOrderBy == 'comment_date' ? 'selected' : ''; ?>>Date</option>
        </select>

        <label for="comment_sort_dir">Direction:</label>
        <select name="comment_sort_dir" id="comment_sort_dir">
            <option value="ASC" <?php echo $commentOrderDirection == 'ASC' ? 'selected' : ''; ?>>Ascending</option>
            <option value="DESC" <?php echo $commentOrderDirection == 'DESC' ? 'selected' : ''; ?>>Descending</option>
        </select>

        <input type="submit" value="Sort">
    </form>

    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Comment</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($comments as $comment): ?>
                <tr>
                    <td><?php echo htmlspecialchars($comment['username']); ?></td>
                    <td><?php echo htmlspecialchars($comment['comment_content']); ?></td>
                    <td><?php echo htmlspecialchars($comment['formatted_date']); ?></td>
                    <td>
                        <!-- 删除按钮始终显示 -->
                        <form action="manage_comments.php" method="post" onsubmit="return confirm('Are you sure you want to delete this comment?');">
                            <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($comment['comment_id']); ?>">
                            <input type="submit" value="Delete">
                        </form>

                        <!-- 根据评论状态显示隐藏或恢复按钮 -->
                        <?php if ($comment['is_hidden']): ?>
                            <form action="manage_comments.php" method="post">
                                <input type="hidden" name="unhide_comment_id" value="<?php echo htmlspecialchars($comment['comment_id']); ?>">
                                <input type="submit" value="Unhide">
                            </form>
                        <?php else: ?>
                            <form action="manage_comments.php" method="post">
                                <input type="hidden" name="hide_comment_id" value="<?php echo htmlspecialchars($comment['comment_id']); ?>">
                                <input type="submit" value="Hide">
                            </form>
                        <?php endif; ?>

                        <!-- 如果已经被 disemvoweled，显示恢复原文按钮 -->
                        <?php if ($comment['is_disemvoweled']): ?>
                            <form action="manage_comments.php" method="post">
                                <input type="hidden" name="restore_comment_id" value="<?php echo htmlspecialchars($comment['comment_id']); ?>">
                                <input type="submit" value="Restore">
                            </form>
                        <?php else: ?>
                            <!-- 否则，显示 Disemvowel 按钮 -->
                            <form action="manage_comments.php" method="post">
                                <input type="hidden" name="disemvowel_comment_id" value="<?php echo htmlspecialchars($comment['comment_id']); ?>">
                                <input type="submit" value="Disemvowel">
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
