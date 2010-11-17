<h1>Playlist items List</h1>

<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Playlist</th>
      <th>Title</th>
      <th>Author</th>
      <th>Mp3</th>
      <th>Time</th>
      <th>Created at</th>
      <th>Updated at</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($playlist_items as $playlist_item): ?>
    <tr>
      <td><a href="<?php echo url_for('audio/show?id='.$playlist_item->getId()) ?>"><?php echo $playlist_item->getId() ?></a></td>
      <td><?php echo $playlist_item->getPlaylistId() ?></td>
      <td><?php echo $playlist_item->getTitle() ?></td>
      <td><?php echo $playlist_item->getAuthor() ?></td>
      <td><?php echo $playlist_item->getMp3() ?></td>
      <td><?php echo $playlist_item->getTime() ?></td>
      <td><?php echo $playlist_item->getCreatedAt() ?></td>
      <td><?php echo $playlist_item->getUpdatedAt() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

  <a href="<?php echo url_for('audio/new') ?>">New</a>
