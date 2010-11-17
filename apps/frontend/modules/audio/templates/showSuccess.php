<table>
  <tbody>
    <tr>
      <th>Id:</th>
      <td><?php echo $playlist_item->getId() ?></td>
    </tr>
    <tr>
      <th>Playlist:</th>
      <td><?php echo $playlist_item->getPlaylistId() ?></td>
    </tr>
    <tr>
      <th>Title:</th>
      <td><?php echo $playlist_item->getTitle() ?></td>
    </tr>
    <tr>
      <th>Author:</th>
      <td><?php echo $playlist_item->getAuthor() ?></td>
    </tr>
    <tr>
      <th>Mp3:</th>
      <td><?php echo $playlist_item->getMp3() ?></td>
    </tr>
    <tr>
      <th>Time:</th>
      <td><?php echo $playlist_item->getTime() ?></td>
    </tr>
    <tr>
      <th>Created at:</th>
      <td><?php echo $playlist_item->getCreatedAt() ?></td>
    </tr>
    <tr>
      <th>Updated at:</th>
      <td><?php echo $playlist_item->getUpdatedAt() ?></td>
    </tr>
  </tbody>
</table>

<hr />

<a href="<?php echo url_for('audio/edit?id='.$playlist_item->getId()) ?>">Edit</a>
&nbsp;
<a href="<?php echo url_for('audio/index') ?>">List</a>
