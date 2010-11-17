<table>
  <tbody>
    <tr>
      <th>Id:</th>
      <td><?php echo $playlist->getId() ?></td>
    </tr>
    <tr>
      <th>Name:</th>
      <td><?php echo $playlist->getName() ?></td>
    </tr>
    <tr>
      <th>Vk user:</th>
      <td><?php echo $playlist->getVkUserId() ?></td>
    </tr>
    <tr>
      <th>Created at:</th>
      <td><?php echo $playlist->getCreatedAt() ?></td>
    </tr>
    <tr>
      <th>Updated at:</th>
      <td><?php echo $playlist->getUpdatedAt() ?></td>
    </tr>
  </tbody>
</table>

<hr />

<a href="<?php echo url_for('playlist/edit?id='.$playlist->getId()) ?>">Edit</a>
&nbsp;
<a href="<?php echo url_for('playlist/index') ?>">List</a>
