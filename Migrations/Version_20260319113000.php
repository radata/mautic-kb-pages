<?php

declare(strict_types=1);

namespace MauticPlugin\MauticKbPagesBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Mautic\IntegrationsBundle\Migration\AbstractMigration;

final class Version_20260319113000 extends AbstractMigration
{
    protected function isApplicable(Schema $schema): bool
    {
        $tableName = $this->concatPrefix('kb_pages');
        if (!$schema->hasTable($tableName)) {
            return false;
        }

        $table = $schema->getTable($tableName);

        return !$table->hasColumn('header_html')
            || !$table->hasColumn('footer_html')
            || !$table->hasColumn('custom_css')
            || !$table->hasColumn('container_width');
    }

    protected function up(): void
    {
        $table = $this->concatPrefix('kb_pages');

        $this->addSql(sprintf('ALTER TABLE %s ADD COLUMN IF NOT EXISTS header_html LONGTEXT DEFAULT NULL', $table));
        $this->addSql(sprintf('ALTER TABLE %s ADD COLUMN IF NOT EXISTS footer_html LONGTEXT DEFAULT NULL', $table));
        $this->addSql(sprintf('ALTER TABLE %s ADD COLUMN IF NOT EXISTS custom_css LONGTEXT DEFAULT NULL', $table));
        $this->addSql(sprintf('ALTER TABLE %s ADD COLUMN IF NOT EXISTS container_width INT DEFAULT NULL', $table));
    }
}
