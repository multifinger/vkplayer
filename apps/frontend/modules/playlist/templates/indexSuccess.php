<h1>Playlists List</h1>

<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Name</th>
      <th>Vk user</th>
      <th>Created at</th>
      <th>Updated at</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($playlists as $playlist): ?>
    <tr>
      <td><a href="<?php echo url_for('playlist/show?id='.$playlist->getId()) ?>"><?php echo $playlist->getId() ?></a></td>
      <td><?php echo $playlist->getName() ?></td>
      <td><?php echo $playlist->getVkUserId() ?></td>
      <td><?php echo $playlist->getCreatedAt() ?></td>
      <td><?php echo $playlist->getUpdatedAt() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

  <a href="<?php echo url_for('playlist/new') ?>">New</a>
