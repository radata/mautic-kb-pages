<?php

declare(strict_types=1);

namespace MauticPlugin\MauticKbPagesBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Mautic\IntegrationsBundle\Migration\AbstractMigration;

final class Version_20260319113000 extends AbstractMigration
{
    private bool $missingHeaderHtml = false;
    private bool $missingFooterHtml = false;
    private bool $missingCustomCss = false;
    private bool $missingContainerWidth = false;

    protected function isApplicable(Schema $schema): bool
    {
        $tableName = $this->concatPrefix('kb_pages');
        if (!$schema->hasTable($tableName)) {
            return false;
        }

        $table = $schema->getTable($tableName);

        $this->missingHeaderHtml    = !$table->hasColumn('header_html');
        $this->missingFooterHtml    = !$table->hasColumn('footer_html');
        $this->missingCustomCss     = !$table->hasColumn('custom_css');
        $this->missingContainerWidth = !$table->hasColumn('container_width');

        return $this->missingHeaderHtml
            || $this->missingFooterHtml
            || $this->missingCustomCss
            || $this->missingContainerWidth;
    }

    protected function up(): void
    {
        $table = $this->concatPrefix('kb_pages');

        if ($this->missingHeaderHtml) {
            $this->addSql(sprintf('ALTER TABLE %s ADD COLUMN header_html LONGTEXT DEFAULT NULL', $table));
        }

        if ($this->missingFooterHtml) {
            $this->addSql(sprintf('ALTER TABLE %s ADD COLUMN footer_html LONGTEXT DEFAULT NULL', $table));
        }

        if ($this->missingCustomCss) {
            $this->addSql(sprintf('ALTER TABLE %s ADD COLUMN custom_css LONGTEXT DEFAULT NULL', $table));
        }

        if ($this->missingContainerWidth) {
            $this->addSql(sprintf('ALTER TABLE %s ADD COLUMN container_width INT DEFAULT NULL', $table));
        }
    }
}
