<table>
    <tr>
        <th><?php echo _AT('jb_username'); ?></th>
        <th><?php echo _AT('jb_employer_name'); ?></th>
        <th><?php echo _AT('jb_email'); ?></th>
        <th><?php echo _AT('jb_company'); ?></th>
        <th><?php echo _AT('jb_description'); ?></th>
        <th><?php echo _AT('jb_website'); ?></th>
        <th><?php echo _AT('jb_last_login'); ?></th>
        <th><?php echo _AT('jb_approval_state'); ?></th>
        <th></th>
    </tr>
    <?php if(!empty($this->employers)): ?>
    <?php foreach($this->employers as $employer): ?>
    <tr>
        <td><?php echo $employer->getUsername(); ?></td>
        <td><?php echo $employer->getName(); ?></td>
        <td><?php echo $employer->getEmail(); ?></td>
        <td><?php echo $employer->getCompany(); ?></td>
        <td><?php echo $employer->getDescription(); ?></td>
        <td><?php echo $employer->getWebsite(); ?></td>
        <td><?php echo $employer->getLastLogin(); ?></td>
        <?php 
            $approval_state = 
        ?>
        <td><?php echo $employer->getApprovalState(); ?></td>
        <td><a href="edit_employer.php"><?php echo _AT('edit'); ?></a></td>
    </tr>
    <?php endforeach; endif; ?>
</table>
