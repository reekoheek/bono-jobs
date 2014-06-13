
<div>
    <a href="<?php echo URL::site('/jobs/null/create') ?>">Create</a>
</div>

<table>
    <tr>
        <th>Expression</th>
        <th>Command</th>
        <th>Next Run</th>
    </tr>
    <?php foreach($entries as $entry): ?>
    <tr>
        <td><?php echo $entry['expression'] ?></td>
        <td><?php echo $entry['command'] ?></td>
        <td><?php echo $entry['next_run']->format('Y-m-d H:i') ?></td>
    </tr>
    <?php endforeach ?>
</table>