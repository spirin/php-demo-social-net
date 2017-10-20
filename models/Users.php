<?php

namespace DemoSocial;

/**
 * Users
 * 
 * Пользователи
 *
 * @author i.spirin
 */
class Users extends Model
{

	public function getAttributes()
	{
		return array(
			'id' => '',
			'email' => '',
			'hash' => '',
			'phone' => '',
			'firstname' => '',
			'lastname' => '',
			'about' => '',
			'borndate' => '',
			'sex' => ''
		);
	}

	public function getTableName()
	{
		return 'users';
	}

	/**
	 * Поиск пользователя по емейлу/телефону и паролю
	 * 
	 * @param type $identity
	 * @param type $password
	 * @return type
	 */
	public function findByIdentity($identity, $password)
	{
		return $this->getConnection()->query(
				'select * from demosocial.users where hash=? and (email=? or phone=?)', array(md5(trim($password)), $identity, $identity)
			)->fetchRow();
	}

	/**
	 * Поиск всех пользователей кроме текущего
	 * 
	 * @return array
	 */
	public function searchOtherUsers($keyword, $offset, $limit)
	{
		$params = array();

		$sql = 'select id,firstname,lastname from demosocial.users';
		if ($keyword)
		{
			$params[] = $keyword . '%';
			$params[] = $keyword . '%';
			$sql .= ' where firstname like ? or lastname like ?';
		}
		else
		{
			$sql .= ' where id != ?';
		}
		$params[] = App::getInstance()->user->id;
		$sql .= ' limit ?,?';
		$params[] = $offset;
		$params[] = $limit;

		$query = $this->getConnection()->query($sql, $params);

		return $query->fetchArray();
	}

}
