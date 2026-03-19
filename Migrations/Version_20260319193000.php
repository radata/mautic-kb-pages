<?php

declare(strict_types=1);

namespace MauticPlugin\MauticKbPagesBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Mautic\IntegrationsBundle\Migration\AbstractMigration;

final class Version_20260319193000 extends AbstractMigration
{
    protected function isApplicable(Schema $schema): bool
    {
        $tableName = $this->concatPrefix('kb_pages');
        if (!$schema->hasTable($tableName)) {
            return false;
        }

        return !$schema->getTable($tableName)->hasColumn('snippets_html');
    }

    protected function up(): void
    {
        $table = $this->concatPrefix('kb_pages');

        $this->addSql(sprintf('ALTER TABLE %s ADD COLUMN IF NOT EXISTS snippets_html LONGTEXT DEFAULT NULL', $table));
    }
}
