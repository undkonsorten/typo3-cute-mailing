<?php

namespace Undkonsorten\CuteMailing\Updates;

use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\ChattyInterface;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

class FolderUpdateWizard implements ChattyInterface, UpgradeWizardInterface
{

    protected string $tablename = 'pages';
    /**
     * @inheritDoc
     */
    public function getIdentifier(): string
    {
        return 'cuteMailing_folderUpdateWizard';
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return 'CuteMailing Folder Update Wizard';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Set doktype to default sys_folder doktype and use module property to mark sysfolder as CuteMailing sysfolder.';
    }

    /**
     * @inheritDoc
     */
    public function executeUpdate(): bool
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->tablename);
        $deleteField = $GLOBALS['TCA']['pages']['ctrl']['delete'];
        $query = $queryBuilder->update($this->tablename)
            ->set('doktype',254)
            ->set('module', 'cute_mailing')
            ->where(
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq(
                        'doktype',
                        116
                    ),
                    $queryBuilder->expr()->eq(
                        $deleteField,
                        0
                    )
                )
            );
        $rows = $query->executeStatement();
        if($rows > 0) {
            $this->output->writeln('Rows changed: '.$rows.'.');
            return true;
        } else {
            $this->output->writeln('Now rows changed.');
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function updateNecessary(): bool
    {
        $updateNeeded = false;
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->tablename);
        $result = $queryBuilder->select('*')
            ->from($this->tablename)
            ->where(
                $queryBuilder->expr()->eq(
                    'doktype',
                    116
                )
            )
            ->execute()->rowCount();
        if ($result > 0) {
            $updateNeeded = true;
            $this->output->writeln('CuteMailing sysfolder has old doktype "116" and should be changed.');
        }
        return $updateNeeded;
    }

    /**
     * @inheritDoc
     */
    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class,
        ];
    }

    /**
     * @var OutputInterface
     */
    protected $output;

    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }
}