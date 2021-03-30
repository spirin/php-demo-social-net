<?php
namespace Source;

/**
 * Friends
 * 
 * Друзья
 *
 * @author i.spirin
 */
class Friends extends Source
{

	public function getAttributes(): array
	{
		return [
			'id' => '',
			'date' => '',
			'fromid' => '',
			'toid' => '',
		];
	}

	public function getTableName(): string
	{
		return 'friends';
	}

	/**
	 * Добавляет в список друзей
	 * 
	 * @param int $from
	 * @param int $to
	 * @return void
	 */
	public function addFriends(int $from, int $to): void
	{
		$this->insert(['fromid' => $from, 'toid' => $to]);
		$this->insert(['fromid' => $to, 'toid' => $from]);
	}

	/**
	 * Удаляет из друзей
	 * 
	 * @param int $from
	 * @param int $to
	 * @return void
	 */
	public function deleteFriends(int $from, int $to): void
	{
		$this->getConnection()->query('delete from demosocial.friends where fromid = ? and toid = ?', [$from, $to]);
		$this->getConnection()->query('delete from demosocial.friends where fromid = ? and toid = ?', [$to, $from]);
	}

	/**
	 * Проверяет наличие дружбы
	 * 
	 * @param int $from
	 * @param int $to
	 * @return bool
	 */
	public function checkFriend(int $from, int $to): bool
	{
		$count = (int) $this->getConnection()->query(
			'select count(id) from demosocial.friends where fromid = ? and toid = ?', [$from, $to]
		)->fetchOne();

		return $count > 0;
	}

	/**
	 * Возвращает список друзей
	 * 
	 * @param int $id
	 * @return array
	 */
	public function searchFriendsFor(int $id): array
	{
		$query = $this->getConnection()->query(
			'select df.id, du.id as uid, du.firstname, du.lastname from'
			. ' demosocial.friends as df left join demosocial.users as du on df.toid = du.id  '
			. 'where fromid=?', [$id]
		);

		return $query->fetchArray();
	}
}
