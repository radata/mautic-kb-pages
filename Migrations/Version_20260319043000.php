<?php

declare(strict_types=1);

namespace MauticPlugin\MauticKbPagesBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Mautic\IntegrationsBundle\Migration\AbstractMigration;

final class Version_20260319043000 extends AbstractMigration
{
    protected function isApplicable(Schema $schema): bool
    {
        return !$schema->hasTable($this->concatPrefix('kb_pages'));
    }

    protected function up(): void
    {
        $table = $this->concatPrefix('kb_pages');

        $this->addSql(
            sprintf(
                'CREATE TABLE %s (
                    id INT AUTO_INCREMENT NOT NULL,
                    is_published TINYINT(1) NOT NULL DEFAULT 1,
                    date_added DATETIME DEFAULT NULL,
                    created_by INT DEFAULT NULL,
                    created_by_user VARCHAR(191) DEFAULT NULL,
                    date_modified DATETIME DEFAULT NULL,
                    modified_by INT DEFAULT NULL,
                    modified_by_user VARCHAR(191) DEFAULT NULL,
                    checked_out DATETIME DEFAULT NULL,
                    checked_out_by INT DEFAULT NULL,
                    checked_out_by_user VARCHAR(191) DEFAULT NULL,
                    title VARCHAR(191) NOT NULL,
                    slug VARCHAR(191) NOT NULL,
                    type VARCHAR(25) NOT NULL,
                    summary LONGTEXT DEFAULT NULL,
                    content LONGTEXT DEFAULT NULL,
                    icon VARCHAR(191) DEFAULT NULL,
                    position INT NOT NULL DEFAULT 0,
                    parent_id INT DEFAULT NULL,
                    PRIMARY KEY(id),
                    INDEX kb_pages_slug_search (slug),
                    INDEX kb_pages_type_published (type, is_published),
                    INDEX kb_pages_parent_published (parent_id, is_published),
                    INDEX kb_pages_position_search (position)
                ) ENGINE=InnoDB',
                $table
            )
        );

        $this->addSql(
            sprintf(
                'ALTER TABLE %s ADD CONSTRAINT kb_pages_parent_fk FOREIGN KEY (parent_id) REFERENCES %s (id) ON DELETE SET NULL',
                $table,
                $table
            )
        );
    }
}
