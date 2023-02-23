<?php

namespace ES;

use ES\config\ConfigurationController;
use ES\Model\Database\MySql;
use ES\Model\Database\RequestSql\TagsSql;

class HtmlService
{
	public static function getHtmlTag($key, $value): ?string
	{
		$tags = MySql::getInstance();

		$brand = $tags->getTags('brand');
		$carcase = $tags->getTags('carcase');
		$transmission = $tags->getTags('transmission');

		switch ($key)
		{
			case 'id':
				return "<input class='admin-input' name='$key' type='hidden' value='$value'>$value";

			case 'images':
				//$form = "<input class='admin-input' name='$key' type='file' multiple accept='image/*' >";
				$data = serialize($value);
				$form = "<div class='admin-images'>";
				// "<div class='admin-images'><input type='hidden' class='admin-input' name='$key' type='file' value='{$data}'>";
				$id = 0;

				foreach ($value as $image)
				{
					$form .= "<input type='radio' id='image{$id}' name='img' value='$image' style='display:none;'><label for='image{$id}'><img class='admin-img' src='$image' alt='$image'></label>";
					$id++;
				}

				return $form . '</div>' . '<label>Изображения:</label><div class="img-list" id="js-file-list"></div><div class="img-list" id="js-file-list"></div><input id="js-file" type="file" name="file[]" multiple accept=".jpg,.jpeg,.png,.gif">';

			case 'productId':
			case 'productPrice':
			case 'price':
			case 'title':
			case 'value':
			case 'fullName':
			case 'phone':
			case 'mail':
			case 'address':
			case 'comment':
			case 'login':
			case 'firstName':
			case 'lastName':
				return "<input name='$key' class='admin-input' type='text' value='$value'>";
			case 'password':
				return "
					<input name='password' type='password' id='show1'> 
					<button class='far fa-eye' type='button' id='show'></button>
					";
			case 'isActive':
				$isActive = ($value === true)
					? "<option selected value='true'>Да</option><option value='false'>Нет</option>"
					: "<option value='true'>Да</option><option selected value='false'>Нет</option>";

				return "<select name='$key'> $isActive </select>";

			case 'status':
				$result = "<select name='$key'>";
				$result .= "<option>$value</option>";
				foreach (ConfigurationController::getConfig('statuses') as $status)
				{
					$result .= ($value !== $status) ? "<option>$status</option>" : '';
				}
				$result .= '</select>';
				return $result;
			case 'role':
				$result = "<select name='$key'>";
				$result .= "<option>$value</option>";
				foreach (ConfigurationController::getConfig('roles') as $status)
				{
					$result .= ($value !== $status) ? "<option>$status</option>" : '';
				}
				$result .= '</select>';
				return $result;
			case 'brandType':
				$result = "<select name='$key'>";
				foreach ($brand as $item)
				{
					$selected = $item->value === $value ? 'selected' : '';
					$result .= "<option $selected value='$item->id'>$item->value</option>";
				}
				$result .= '</select>';
				return $result;

			case 'carcaseType':
				$result = "<select name='$key'>";
				foreach ($carcase as $item)
				{
					$selected = $item->value === $value ? 'selected' : '';
					$result .= "<option $selected value='$item->id'>$item->value</option>";
				}
				$result .= '</select>';
				return $result;

			case 'transmissionType':
				$result = "<select name='$key'>";
				foreach ($transmission as $item)
				{
					$selected = $item->value === $value ? 'selected' : '';
					$result .= "<option $selected value='$item->id'>$item->value</option>";
				}
				$result .= '</select>';
				return $result;

			case 'fullDesc':
				return "<textarea rows = '10' cols='45' name='$key'>$value</textarea>";

			case 'dateUpdate':
			case 'dateCreation':
				return "<input type='hidden' class='admin-input' name='$key' type='text' value='$value'>$value";

			default:
				return $value;
		}
	}

	public static function getPathImagesById(int $id): array
	{
		$path = ROOT . "/public/tmp-autoimg/$id";
		if (!file_exists($path))
		{
			return []; // @todo добавить грустную машину
		}
		$files = scandir($path);
		$images = [];
		foreach ($files as $file)
		{
			if (!preg_match('~^[0-9A-Za-z].[A-Za-z]+$~', $file))
			{
				continue;
			}
			$images[] = $file;
		}
		return $images;
	}
}