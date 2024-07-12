<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240712051125 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cliente (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, cli_nombres VARCHAR(128) NOT NULL, cli_apepat VARCHAR(64) NOT NULL, cli_apemat VARCHAR(64) DEFAULT NULL, cli_dni VARCHAR(8) DEFAULT NULL, cli_correo VARCHAR(255) DEFAULT NULL, cli_telefono VARCHAR(9) NOT NULL, cli_direccion CLOB NOT NULL, cli_estado BOOLEAN NOT NULL)');
        $this->addSql('CREATE TABLE proyecto (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, pyt_cliente_id INTEGER NOT NULL, pyt_nombre VARCHAR(64) NOT NULL, pyt_primercontacto DATE NOT NULL, pyt_descripcion CLOB NOT NULL, pyt_estado BOOLEAN NOT NULL, CONSTRAINT FK_6FD202B9F74609A0 FOREIGN KEY (pyt_cliente_id) REFERENCES cliente (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_6FD202B9F74609A0 ON proyecto (pyt_cliente_id)');
        $this->addSql('CREATE TABLE usuario (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, usu_correo VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, usu_estado BOOLEAN NOT NULL, is_verified BOOLEAN NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_USU_CORREO ON usuario (usu_correo)');
        $this->addSql('CREATE TABLE version (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, vs_proyecto_id INTEGER DEFAULT NULL, vs_descripcion VARCHAR(64) NOT NULL, vs_fechainicio DATE NOT NULL, vs_fechafinestimada DATE NOT NULL, vs_duracion DOUBLE PRECISION NOT NULL, vs_planificacion CLOB DEFAULT NULL, vs_presupuesto CLOB NOT NULL, vs_alcance CLOB NOT NULL, vs_estado BOOLEAN NOT NULL, CONSTRAINT FK_BF1CD3C317653455 FOREIGN KEY (vs_proyecto_id) REFERENCES proyecto (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_BF1CD3C317653455 ON version (vs_proyecto_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE cliente');
        $this->addSql('DROP TABLE proyecto');
        $this->addSql('DROP TABLE usuario');
        $this->addSql('DROP TABLE version');
    }
}
