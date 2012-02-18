<?php
namespace Quipp\Module\AccountManagement;
use Quipp\Core;

/**
 * An instance represents one group to handle reads and updates
 */
class Group {
    /**
     * @type Quipp\Core
     */
    protected $_core;

    /**
     * @type int
     * @db `sysUGroups`.`itemID`
     */
    protected $_id;

    /**
     * @param Quipp\Core
     * @param int
     */
    public function __construct(Core $core, $id) {
        $this->_core = $core;
        $this->_id   = $id;
    }

    /**
     * @param User
     * @return bool
     */
    public function addUser(User $user) {
        return (false === $this->_core->db()->query(sprintf("INSERT INTO `sysUGLinks` (`userID`, `groupID`) VALUES (%d, %d)", $user->getID(), $this->_id)) ? false : true);
    }
}