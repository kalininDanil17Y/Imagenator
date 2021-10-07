<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Images extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('images');
        $table->addColumn('uuid', 'char', ['limit' => 80])
            ->addColumn('name', 'char', ['limit' => 90])
            ->addColumn('dateUploaded', 'datetime')
            ->addColumn('ipAddress', 'char', ['limit' => 25])
            ->create();

        $this->execute('ALTER TABLE `images` MODIFY COLUMN `dateUploaded` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
    }
}
