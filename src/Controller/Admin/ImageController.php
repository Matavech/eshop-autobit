<?php

namespace ES\Controller\Admin;
use ES\Model\Database\MySql;

class ImageController extends AdminController
{
	public function clearImagesAction(): void
	{
		MySql::clearUnusedImages();
		header('Location: /admin/');
	}

	public function uploadImagesAction(): string
	{
		if (!isset($_SESSION['USER']))
		{
			header('Location: /login/');
		}

		$input_name = 'file';

		if (!isset($_FILES[$input_name]))
		{
			exit;
		}

		// Разрешенные расширения файлов.
		$allow = ['jpg', 'jpeg', 'png', 'gif'];

		// URL до временной директории.
		$url_path = '/uploads/tmp/';

		// Полный путь до временной директории.
		$tmp_path = $_SERVER['DOCUMENT_ROOT'] . $url_path;

		if (!is_dir($tmp_path))
		{
			mkdir($tmp_path, 0777, true);
		}

		// Преобразуем массив $_FILES в удобный вид для перебора в foreach.
		$files = [];
		$diff = count($_FILES[$input_name]) - count($_FILES[$input_name], COUNT_RECURSIVE);
		if ($diff == 0)
		{
			$files = [$_FILES[$input_name]];
		}
		else
		{
			foreach ($_FILES[$input_name] as $k => $l)
			{
				foreach ($l as $i => $v)
				{
					$files[$i][$k] = $v;
				}
			}
		}

		$response = [];
		foreach ($files as $key => $file)
		{
			$error = $data = '';

			// Проверим на ошибки загрузки.
			$ext = mb_strtolower(mb_substr(mb_strrchr(@$file['name'], '.'), 1));

			if (!empty($file['error']) || empty($file['tmp_name']) || $file['tmp_name'] == 'none')
			{
				$error = 'Не удалось загрузить файл.';
			}
			elseif (empty($file['name']) || !is_uploaded_file($file['tmp_name']))
			{
				$error = 'Не удалось загрузить файл.';
			}
			elseif (empty($ext) || !in_array($ext, $allow))
			{
				$error = 'Недопустимый тип файла';
			}
			else
			{
				$info = @getimagesize($file['tmp_name']);
				if (empty($info[0]) || empty($info[1]) || !in_array($info[2], [1, 2, 3]))
				{
					$error = 'Недопустимый тип файла';
				}
				else
				{
					// Перемещаем файл в директорию с новым именем.
					$name = time() . '-' . mt_rand(1, 9999999999);
					$src = $tmp_path . $name . '.' . $ext;
					$thumb = $tmp_path . $name . '-thumb.' . $ext;

					if (move_uploaded_file($file['tmp_name'], $src))
					{
						// Создание миниатюры.
						switch ($info[2])
						{
							case 1:
								$im = imageCreateFromGif($src);
								imageSaveAlpha($im, true);
								break;
							case 2:
								$im = imageCreateFromJpeg($src);
								break;
							case 3:
								$im = imageCreateFromPng($src);
								imageSaveAlpha($im, true);
								break;
						}

						$width = $info[0];
						$height = $info[1];

						// Высота превью 100px, ширина рассчитывается автоматически.
						$h = 100;
						$w = ($h > $height) ? $width : ceil($h / ($height / $width));
						$tw = ceil($h / ($height / $width));
						$th = ceil($w / ($width / $height));

						$new_im = imageCreateTrueColor($w, $h);
						if ($info[2] == 1 || $info[2] == 3)
						{
							imagealphablending($new_im, true);
							imageSaveAlpha($new_im, true);
							$transparent = imagecolorallocatealpha($new_im, 0, 0, 0, 127);
							imagefill($new_im, 0, 0, $transparent);
							imagecolortransparent($new_im, $transparent);
						}

						if ($w >= $width && $h >= $height)
						{
							$xy = [ceil(($w - $width) / 2), ceil(($h - $height) / 2), $width, $height];
						}
						elseif ($w >= $width)
						{
							$xy = [ceil(($w - $tw) / 2), 0, ceil($h / ($height / $width)), $h];
						}
						elseif ($h >= $height)
						{
							$xy = [0, ceil(($h - $th) / 2), $w, ceil($w / ($width / $height))];
						}
						elseif ($tw < $w)
						{
							$xy = [ceil(($w - $tw) / 2), ceil(($h - $h) / 2), $tw, $h];
						}
						else
						{
							$xy = [0, ceil(($h - $th) / 2), $w, $th];
						}

						imageCopyResampled($new_im, $im, $xy[0], $xy[1], 0, 0, $xy[2], $xy[3], $width, $height);

						// Сохранение.
						switch ($info[2])
						{
							case 1:
								imageGif($new_im, $thumb);
								break;
							case 2:
								imageJpeg($new_im, $thumb, 100);
								break;
							case 3:
								imagePng($new_im, $thumb);
								break;
						}

						imagedestroy($im);
						imagedestroy($new_im);

						$checked = $key === 0 ? 'checked' : '';

						// Вывод в форму: превью, кнопка для удаления и скрытое поле.
						$data = '
							<div class="img-item">
								<input type="hidden" name="images[]" value="'
							. $name
							. '.'
							. $ext
							. '" >
								<input id="'
							. $name
							. '" type="radio" name="main-image" value="'
							. $name
							. '.'
							. $ext
							. '" '
							. $checked
							. ' style="display:none;">
								<label for="'
							. $name
							. '" class="">
									<img class="admin-img" src="'
							. $url_path
							. $name
							. '-thumb.'
							. $ext
							. '">
									<a href="#" class="delete-icon" onclick="remove_img(this); return false;"></a>
								</label>
							</div>';
					}
					else
					{
						$error = 'Не удалось загрузить файл.';
					}
				}
			}

			$response[] = ['error' => $error, 'data' => $data];
		}

		// Ответ в JSON.
		header('Content-Type: application/json');
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
		exit();
	}
}