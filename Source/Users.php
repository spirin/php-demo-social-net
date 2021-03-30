<?php
namespace Source;

use Lib\UserSession;

/**
 * Users
 * 
 * Пользователи
 *
 * @author i.spirin
 */
class Users extends Source
{

	public function getAttributes(): array
	{
		return [
			'id' => '',
			'email' => '',
			'hash' => '',
			'phone' => '',
			'firstname' => '',
			'lastname' => '',
			'about' => '',
			'borndate' => '',
			'sex' => '',
		];
	}

	public function getTableName(): string
	{
		return 'users';
	}

	/**
	 * Поиск пользователя по емейлу/телефону и паролю
	 * 
	 * @param string $identity
	 * @param string $password
	 * @return array
	 */
	public function findByIdentity(string $identity, string $password): array
	{
		return $this->getConnection()->query(
			'select * from demosocial.users where hash=? and (email=? or phone=?)', [md5(trim($password)), $identity, $identity]
		)->fetchRow();
	}

	/**
	 * Поиск всех пользователей кроме текущего
	 * 
	 * @param string|null $keyword
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 */
	public function searchOtherUsers(?string $keyword, int $offset, int $limit): array
	{
		$params = [];
		$sql = 'select id,firstname,lastname from demosocial.users';
		$params[] = UserSession::get()->id;
		if ($keyword) {
			$params[] = $keyword . '%';
			$params[] = $keyword . '%';
			$sql .= ' where id != ? and firstname like ? or lastname like ?';
		} else {
			$sql .= ' where id != ?';
		}
		$sql .= ' limit ?,?';
		$params[] = $offset;
		$params[] = $limit;

		$query = $this->getConnection()->query($sql, $params);

		return $query->fetchArray();
	}
}
