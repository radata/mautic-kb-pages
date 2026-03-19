<?php

declare(strict_types=1);

namespace MauticPlugin\MauticKbPagesBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Mautic\IntegrationsBundle\Migration\AbstractMigration;

final class Version_20260319193000 extends AbstractMigration
{
    private bool $missingSnippetsHtml = false;

    protected function isApplicable(Schema $schema): bool
    {
        $tableName = $this->concatPrefix('kb_pages');
        if (!$schema->hasTable($tableName)) {
            return false;
        }

        $this->missingSnippetsHtml = !$schema->getTable($tableName)->hasColumn('snippets_html');

        return $this->missingSnippetsHtml;
    }

    protected function up(): void
    {
        if (!$this->missingSnippetsHtml) {
            return;
        }

        $table = $this->concatPrefix('kb_pages');

        $this->addSql(sprintf('ALTER TABLE %s ADD COLUMN snippets_html LONGTEXT DEFAULT NULL', $table));
    }
}
