<?php

/**
 * Возвращает список товаров
 * @param int $page страница. от 1
 * @param string $sorting имя столбца для сортировки
 * @param string $sorting_type ASC/DESC
 * @return array
 */
function getGoods($page = 1, $sorting = '', $sorting_type = 'ASC')
{

	global $mysqli;

	$more = 0;
	$perPage = 50;
	$items = [];
	$limit = ($page - 1) * $perPage;

	$allowedSorting = array_keys(_sorting);
	$key = array_search($sorting, $allowedSorting);
	$sorting = $allowedSorting[$key];
	$sorting_type = ($sorting_type == 'DESC') ? 'DESC' : 'ASC';


	// Защита от тех, кто пытается limit -99999,50 сделать
	if ($limit >= 0) {

		$items =
			$mysqli->query("
				select
						g.ID,
						g.Name,
						g.Description,
						g.PhotoURL,
						g.Price
					from goods g
					
				WHERE
					g.Deleted=0
					
				ORDER BY 
					g.{$sorting} {$sorting_type}
					
				LIMIT 
					{$limit}, {$perPage}
			  
			")->fetch_all(MYSQLI_ASSOC);


		$total_count =
			$mysqli->query("
				SELECT
						count(g.ID)
					FROM goods g
			")->fetch_row()[0];


		$more =
			$total_count > $limit + $perPage;

	}

	return [
		'sorting' => $sorting,
		'sorting_type' => $sorting_type,
		'items' => $items,
		'more' => $more
	];
}

function getPrice($float) {

	$price_parts = explode('.', $float);

	$price = number_format($price_parts[0], 0, '.', ' ');

	if (intval($price_parts[1]))
		$price .= '.' . $price_parts[1];

	return $price;
}