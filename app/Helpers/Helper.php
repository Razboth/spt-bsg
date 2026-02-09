<?php

use App\Models\Setting\AppNumberFormat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTables;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

function setCurrency($value)
{
    return number_format($value);
}

function decimalFormat($value)
{
    return $value != 0 && $value != 100 ? number_format($value, 2) : $value;
}

function resetCurrency($value)
{
    list($value) = explode("]", str_replace("[>", "", $value));
    $value = $value == "" ? "0" : $value;

    $ret = substr($value, 0, 1) == "." ? "0" . $value : $value;
    $ret = str_replace(",", "", $ret);

    return $ret;
}

function convertTimeToSeconds($time = '')
{
    if ($time == '')
        return 0;

    $time = explode(":", $time);
    $seconds = $time[0] * 3600;
    $seconds += $time[1] * 60;
    $seconds += $time[2];

    return $seconds;
}

function convertMinutesToTime($minute, $format = '%02d:%02d')
{
    if ($minute < 1) {
        return;
    }
    $hours = floor($minute / 60);
    $minutes = ($minute % 60);
    return sprintf($format, $hours, $minutes);
}

function setDate($value, $format = null)
{
    return $value != null && $value != '0000-00-00' ? Carbon::createFromFormat('Y-m-d', $value)->translatedFormat($format ?: 'd/m/Y') : '';
}

function resetDate($value)
{
    return $value != null ? Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d') : '';
}

function setDateTime($value, $format = null)
{
    return $value != null ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format($format ?: 'd/m/Y H:i') : '';
}

function resetDateTime($value)
{
    return $value != null ? Carbon::createFromFormat('d/m/Y H:i', $value)->format('Y-m-d H:i:s') : '';
}

function defaultStatus($isEnglish = false)
{
    return $isEnglish ? array('t' => 'Active', 'f' => 'Inactive') : array('t' => 'Aktif', 'f' => 'Tidak Aktif');
}

function defaultGender($isEnglish = false)
{
    return $isEnglish ? array('M' => 'Male', 'F' => 'Female') : array('M' => 'Laki-Laki', 'F' => 'Perempuan');
}

function yesOrNoOption($isEnglish = false)
{
    return $isEnglish ? array('t' => 'Yes', 'f' => 'No') : array('t' => 'Ya', 'f' => 'Tidak');
}

function defaultStatusApproval()
{
    return array('t' => 'Disetujui', 'f' => 'Ditolak');
}

function uploadOne(UploadedFile $uploadedFile, $folder = null, $disk = 'public', $filename = null)
{
    $name = !is_null($filename) ? $filename : Str::random(25);

    $file = $uploadedFile->storeAs($folder, $name . '.' . $uploadedFile->getClientOriginalExtension(), $disk);

    return $file;
}

function uploadFile($image, $name, $folder, $resize = false, $width = 300, $height = 300)
{

    if ($image) {
        // Make a file path where image will be stored [ folder path + file name + file extension]
        $filePath = $folder . $name . '.' . $image->getClientOriginalExtension();
        // Upload image
        uploadOne($image, $folder, 'public', $name);
        // Set user profile image path in database to filePath
        $picture = $filePath;

        //RESIZE
        if ($resize) {
            $thumbnailpath = public_path('storage/' . $picture);
            $img = Image::make($thumbnailpath)->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($thumbnailpath);
        }

        return $picture;

    }
}

function deleteFile($path): bool
{
    if (Storage::exists($path))
        Storage::delete($path);

    return true;
}

function defaultUploadFile($model, $request, $path, $filename, $fieldName = 'filename', $resize = ''): void
{
    if ($request->get('isDelete_' . $fieldName) == 't') {
        deleteFile($path . $model->$fieldName);
        $model->update([
            $fieldName => null,
        ]);
    }

    if ($request->get($fieldName . "_remove")) {
        deleteFile($path . $model->$fieldName);
        $model->update([
            $fieldName => null,
        ]);
    }
    if ($request->hasFile($fieldName)) {
        $resize = $resize ?? false;
        $extension = $request->file($fieldName)->extension();
        if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg')
            $resize = $resize ?? true;

        $resUpload = uploadFile(
            $request->file($fieldName),
            $filename,
            $path,
            $resize
        );

        $model->update([
            $fieldName => $resUpload,
        ]);
    }
}


function generatePagination($items, $form = 'form-filter'): void
{
    $pagination = $items->withQueryString()->onEachSide(0)->links();
    $arrNumber = array("10", "50", "100", "500", "1000");
    ?>
    <div class="d-flex justify-content-between align-items-center">
        <div class="ml-1">
            <select name="paginate" form="<?= $form ?>" onchange="document.getElementById('<?= $form ?>').submit();"
                    class="form-control">
                <?php
                foreach ($arrNumber as $key => $value) {
                    $selected = 10;
                    if (!empty($_GET['paginate']))
                        $selected = $_GET['paginate'] == $value ? 'selected' : '';
                    echo "<option $selected value=\"$value\">$value</option>";
                }
                ?>
            </select>
        </div>
        <div class="ml-1 <?= $items->total() <= $items->perPage() ? 'mb-1' : '' ?>">
            <i style="font-size: 12px">Showing
                <?= $items->firstItem() ?> to
                <?= $items->lastItem() ?> of
                <?= $items->total() ?> entries
            </i>
        </div>
        <div>
            <?php
            if ($items->total() >= $items->perPage()) {
                echo $pagination;
            } else {
                ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-end mt-1 mr-2 mb-2">
                        <li class="page-item prev"><a class="page-link"></a></li>
                        <li class="page-item active" aria-current="page">
                            <a class="page-link">1</a>
                        </li>
                        <li class="page-item next"><a class="page-link"></a></li>
                    </ul>
                </nav>
                <?php
            }
            ?>
        </div>

    </div>
    <br>
    <?php
}

function numToRoman($number)
{
    $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
    $returnValue = '';
    while ($number > 0) {
        foreach ($map as $roman => $int) {
            if ($number >= $int) {
                $number -= $int;
                $returnValue .= $roman;
                break;
            }
        }
    }
    return $returnValue;
}

function numToMonth($number, $acronym = false)
{
    \Carbon\Carbon::setLocale('id');
    $date = \Carbon\Carbon::parse('2021-' . $number . '-01');
    //change to month name 3 letter
    $monthName = $date->translatedFormat($acronym ? 'M' : 'F');

    return $monthName;
}

function numToAlpha($number)
{
    $numeric = $number % 26;
    $letter = chr(65 + $numeric);
    $num2 = intval($number / 26);
    if ($num2 > 0) {
        return numToAlpha($num2 - 1) . $letter;
    } else {
        return $letter;
    }
}

function getStatusApproval($value)
{
    if ($value == 't') {
        $badge = 'success';
        $text = 'Approved';
    } else if ($value == 'p') {
        $badge = 'info';
        $text = 'Pending';
    } else if ($value == 'f') {
        $badge = 'danger';
        $text = 'Rejected';
    } else {
        $badge = 'secondary';
        $text = 'Need Action';
    }

    return "<div class='badge badge-$badge'>$text</div>";
}

function getIcon($value, $size = 'fa-2x')
{
    $extension = pathinfo(strtolower($value), PATHINFO_EXTENSION);

    $arrImage = array(
        'doc' => 'word',
        'docx' => 'word',
        'pdf' => 'pdf',
        'xls' => 'excel',
        'xlsx' => 'excel',
        'csv' => 'csv',
        'jpg' => 'image',
        'jpeg' => 'image',
        'png' => 'image',
        'jfif' => 'image',
    );

    $image = $arrImage[$extension];

    $icon = "<i class=\"fas $size fa-file-$image\"></i>";

    return $icon;
}

function getInitial($sentences, $length = 2)
{
    $words = explode(' ', $sentences);
    $acronym = '';

    foreach ($words as $w) {
        $acronym .= $w[0] ?? '';
    }

    return strtoupper(substr($acronym, 0, $length));
}

function getAllBadges()
{
    $arr = array(
        'primary',
        'success',
        'danger',
        'warning',
        'info',
        'dark',
    );

    return $arr;
}

function getParameterMenu(string $pathMenu)
{
    list($modul, $menu) = explode('/', $pathMenu);
    return DB::table('app_menus as t1')->join('app_moduls as t2', 't1.app_modul_id', '=', 't2.id')
        ->where('t2.target', $modul)
        ->where('t1.target', $menu)
        ->first()->parameter;
}

function menu_path()
{
    $arrURL = explode('/', \Request::url()); //GET URL
    $modul = !empty($arrURL[3]) ? $arrURL[3] : ''; //GET MODUL
    $menu = !empty($arrURL[4]) ? explode('?', $arrURL[4])[0] ?? '' : ''; //GET MENU WITHOUT PARAM

    return $modul . '/' . $menu;
}

/**
 * @throws Exception
 */
function generateDatatable(
    $query,
    $filter,
    $customFields = [],
    $isModal = false,
    $isShowRoute = false,
    $customPrefix = null,
    $arrCustomAction = [],
    $iconShow = '',
    $editable = true,
    $deleteable = true,
)
{
    //generate datatables with filter
    $dataTables = DataTables::of($query);
    if ($filter)
        $dataTables->filter($filter);

    //generate custom fields
    foreach ($customFields as $field) {
        if ($field['type'] == 'date') {
            $data = function ($model) use ($field) {
                return $model->{$field['name']} != '0000-00-00' && $model->{$field['name']} != null ? setDate($model->{$field['name']}) : '';
            };
        } else if ($field['type'] == 'time') {
            $data = function ($model) use ($field) {
                return substr($model->{$field['name']} ?? '', 0, 5);
            };
        } else if ($field['type'] == 'datetime') {
            $data = function ($model) use ($field) {
                return $model->{$field['name']} != '0000-00-00 00:00:00' && $model->{$field['name']} != null ? setDateTime($model->{$field['name']}) : '';
            };
        } else if ($field['type'] == 'masters') {
            $data = function ($model) use ($field) {
                return $field['masters'][$model->{$field['name']}] ?? '';
            };
        } else if ($field['type'] == 'master_relationship') {
            $field['master_field'] = $field['master_field'] ?? 'name';
            $data = function ($model) use ($field) {
                $value = $model->{$field['masters']}->{$field['master_field']} ?? $field['null_value'] ?? '';
                if (!empty($field['master_type_value'])) {
                    return $field['master_type_value'] == 'currency' ? setCurrency((int)$value) : $value;
                }
                return $value;
            };
        } else if ($field['type'] == 'multiple_relationship') {
            $field['master_field'] = $field['master_field'] ?? 'name';
            $data = function ($model) use ($field) {
                return $model->{$field['masters']}->{$field['second_masters']}->{$field['master_field']} ?? '';
            };
        } else if ($field['type'] == 'filename') {
            $data = function ($model) use ($field) {
                return $model->{$field['name']} ? view('components.datatables.download', [
                    'url' => $model->{$field['name']}
                ]) : '';
            };
        } else if ($field['type'] == 'status') {
            $data = function ($model) use ($field) {
                return view('components.views.status', [
                    'status' => $model->{$field['name']},
                    'english' => $field['english'] ?? false,
                ]);
            };
        } else if ($field['type'] == 'approved_status') {
            $data = function ($model) use ($field) {
                $arr = [
                    'status' => $model->{$field['name']},
                    'type' => $field['approved_type'] ?? 1,
                ];

                if (!empty($field['isCanApprove'])) {
                    $arr = array_merge($arr, [
                        'url' => route(Str::replace('/', '.', menu_path()) . '.approve', [$model->id, $field['approved_type'] ?? 1]),
                    ]);
                }
                return view('components.views.approve-status', $arr);
            };
        } else if ($field['type'] == 'yesorno') {
            $data = function ($model) use ($field) {
                return view('components.views.yes-or-no', [
                    'value' => $model->{$field['name']},
                    'english' => $field['english'] ?? false,
                ]);
            };
        } else if ($field['type'] == 'color') {
            $data = function ($model) use ($field) {
                return view('components.datatables.color', [
                    'color' => $model->{$field['name']},
                ]);
            };
        } else if ($field['type'] == 'custom_action') {
            $data = function ($model) use ($field) {
                $ids = [];
                foreach ($field['ids'] as $id) {
                    $ids[] = $model->{$id};
                }

                $action = view('components.views.action', [
                    'url_slot' => route(Str::replace('/', '.', menu_path()) . '.' . $field['route'], $ids),
                    'icon_slot' => $field['icon'] ?? 'fa fa-list',
                    'isModalSlot' => $field['isModal'] ?? false,
                    'isTargetBlankSlot' => $field['isTargetBlank'] ?? false,
                ]);

                return $action;
            };
        } elseif ($field['type'] == 'photo') {
            $data = function ($model) use ($field) {
                return view('components.views.photo', [
                    'photo' => $model->{$field['name']},
                ]);
            };
        } else if ($field['type'] == 'gender') {
            $data = function ($model) use ($field) {
                return $model->{$field['name']} == 'M' ? 'Laki-laki' : 'Perempuan';
            };
        } else if ($field['type'] == 'currency') {
            $data = function ($model) use ($field) {
                return setCurrency($model->{$field['name']}) ?? '';
            };
        } else {
            $data = $field['value'];
        }

        if (!empty($field['isAdd'])) {
            $dataTables->addColumn($field['name'], $data);
        } else {
            $dataTables->editColumn($field['name'], $data);
        }
    }

    //generate action
    $dataTables->addColumn('action', function ($model) use ($isModal, $isShowRoute, $customPrefix, $iconShow, $arrCustomAction, $deleteable, $editable) {
        $editPrefix = $customPrefix ? 'edit-' . $customPrefix : 'edit';
        $destroyPrefix = $customPrefix ? 'destroy-' . $customPrefix : 'destroy';
        $showPrefix = $customPrefix ? 'show-' . $customPrefix : 'show';
        $arrAction = [
            'menu_path' => menu_path(),
            'isModal' => $isModal,
        ];

        if ($model->approved_status != 't') {
            if ($editable) {
                $arrAction = array_merge($arrAction, [
                    'url_edit' => route(Str::replace('/', '.', menu_path()) . '.' . $editPrefix, $model->id),
                ]);
            }

            if ($deleteable) {
                $arrAction = array_merge($arrAction, [
                    'url_destroy' => route(Str::replace('/', '.', menu_path()) . '.' . $destroyPrefix, $model->id),
                ]);
            }
        }

        if (!empty($arrCustomAction)) {
            $actions = [];
            foreach ($arrCustomAction as $action) {
                $ids = [];
                foreach ($action['ids'] as $id) {
                    $ids[] = $model->{$id};
                }

                $actions[] = [
                    'url' => route(Str::replace('/', '.', menu_path()) . '.' . $action['route'], $ids),
                    'icon' => $action['icon'],
                    'class-icon' => $action['class-icon'] ?? 'info',
                    'isModal' => $action['isModal'] ?? '',
                    'title' => $action['title'] ?? '',
                    'size' => $action['size'] ?? '',
                ];
            }

            $arrAction = array_merge($arrAction, ['customAction' => $actions]);
        }

        if (!empty($isShowRoute)) {
            $arrAction = array_merge($arrAction, [
                'url_show' => route(Str::replace('/', '.', menu_path()) . '.' . $showPrefix, $model->id),
            ]);
            if (!empty($iconShow)) {
                $arrAction = array_merge($arrAction, [
                    'icon_show' => $iconShow,
                ]);
            }
        }

        return view('components.views.action', $arrAction);
    });

    return $dataTables->addIndexColumn()->make();
}

function submitDataHelper($function, bool $isModal = false, string $route = 'index', array $param = []): JsonResponse|RedirectResponse
{
    try {
        DB::transaction(function () use ($function) {
            $function();
        });
        $status = 'success';
        $message = 'Data berhasil disimpan';
    } catch (Exception $e) {
        $status = 'error';
        $message = 'Error: ' . $e->getMessage();

        Log::error($e->getMessage());
    }

    if ($isModal) {
        return response()->json([
            'success' => $message,
            'url' => route(Str::replace('/', '.', menu_path()) . '.' . $route, $param)
        ]);
    } else {
        if ($status == 'success') {
            Alert::success('Success', $message);
        } else {
            Alert::error('Error', $message);

            Log::error($message);
        }

        return redirect()->route(Str::replace('/', '.', menu_path()) . '.' . $route, $param);
    }
}

function convertExcelDate($dateValue)
{
    if (empty($dateValue)) {
        return null;
    }

    // If the value is numeric, assume it's an Excel serial date
    if (is_numeric($dateValue)) {
        try {
            $date = ExcelDate::excelToDateTimeObject($dateValue);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            return null; // Handle invalid Excel date
        }
    }

    // If it's already a string, assume it's in Y-m-d format or another recognizable format
    try {
        return Carbon::parse($dateValue)->format('Y-m-d');
    } catch (\Exception $e) {
        return null; // Handle invalid date string
    }
}

function deleteDataHelper($function, string $route = '', array $param = []): RedirectResponse
{
    try {
        DB::transaction(function () use ($function) {
            $function();
        });

        $status = 'success';
        $message = 'Data berhasil dihapus';
    } catch (Exception $e) {

        $status = 'error';
        $message = 'Error: ' . $e->getMessage();

        Log::error($e->getMessage());
    }

    if ($status == 'success') {
        Alert::success('Success', $message);
    } else {
        Alert::error('Error', $message);

        Log::error($message);
    }

    if ($route) {
        return redirect()->route(Str::replace('/', '.', menu_path()) . '.' . $route, $param);
    } else {
        return redirect()->back();
    }
}

function importHelper($classImport, $request, $route = 'index')
{
    try {
        if ($request->hasFile('filename')) {
            Excel::import($classImport, $request->file('filename'));

            return response()->json([
                'success' => 'Import data telah selesai',
                'type' => 'import',
                'url' => route(Str::replace('/', '.', menu_path()) . '.' . $route),
                'url_log' => asset('storage/log/' . $classImport->logname),
            ]);
        }
    } catch (Exception $e) {
        //implement file and line too
        Log::error('Import Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());

        return response()->json([
            'success' => 'Gagal Import ' . $e->getMessage(),
            'url' => route(Str::replace('/', '.', menu_path()) . '.' . $route),
        ]);
    }
}

function generateFormatNumber($code, $table, $field = 'number', $additionalQuery = null): string
{
    $formatNumber = AppNumberFormat::where('code', $code)->first();
    $query = DB::table($table);
    $where = '1=1';
    $order = '';
    $lengthStart = 1;

    $formatParts = [];
    $sequenceIndex = -1;
    for ($i = 1; $i <= 5; $i++) {
        $type = $formatNumber->{"var{$i}_type"};
        $value = $formatNumber->{"var{$i}_value"};

        if ($type == 'sequence') {
            $length = $value;
            $order .= " SUBSTR($field, $lengthStart, $length) DESC";
            $seqStart = $lengthStart;
            $seqEnd = $length;
            $sequenceIndex = count($formatParts); // Store the index of sequence part
            $formatParts[] = ['type' => 'sequence', 'length' => $length];
        } elseif ($type == 'code') {
            $length = strlen($value);
            $where .= " AND SUBSTR($field, $lengthStart, $length) = '$value'";
            $formatParts[] = ['type' => 'code', 'value' => $value, 'length' => $length];
        } elseif ($type == 'month') {
            $month = $value == 'roman' ? numToRoman(date('m')) : date('m');
            $length = strlen($month);
            $where .= " AND SUBSTR($field, $lengthStart, $length) = '$month'";
            $formatParts[] = ['type' => 'month', 'value' => $month, 'length' => $length];
        } elseif ($type == 'year') {
            $year = $value == '2digit' ? date('y') : date('Y');
            $length = strlen($year);
            $where .= " AND SUBSTR($field, $lengthStart, $length) = '$year'";
            $formatParts[] = ['type' => 'year', 'value' => $year, 'length' => $length];
        }

        $lengthStart += $length + 1;
    }

    $query->whereRaw($where);
    if (is_callable($additionalQuery)) {
        $query = $additionalQuery($query);
    }
    $query->orderByRaw($order);
    $resultData = $query->first()->$field ?? '';

    // Update sequence value in formatParts
    if ($sequenceIndex != -1) {
        $sequencePart = &$formatParts[$sequenceIndex];
        $sequencePart['value'] = Str::padLeft(intval(Str::substr($resultData, $seqStart, $sequencePart['length'])) + 1, $sequencePart['length'], '0');
    }

    // Construct final number
    $formattedNumber = collect($formatParts)->map(function ($part) use ($formatNumber) {
        return $part['value'] ?? '';
    })->implode($formatNumber->delimiter);

    return $formattedNumber;
}


function numToWord($number): string
{
    $satuan = array('nol', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas');

    if ($number < 12) {
        return $satuan[$number];
    } elseif ($number < 20) {
        return $satuan[$number - 10] . " belas";
    } elseif ($number < 100) {
        $puluhan = floor($number / 10);
        $sisa = $number % 10;
        return $satuan[$puluhan] . " puluh " . ($sisa ? numToWord($sisa) : '');
    } elseif ($number < 200) {
        return "seratus " . ($number % 100 ? numToWord($number - 100) : '');
    } elseif ($number < 1000) {
        $ratusan = floor($number / 100);
        return $satuan[$ratusan] . " ratus " . ($number % 100 ? numToWord($number % 100) : '');
    } elseif ($number < 2000) {
        return "seribu " . ($number % 1000 ? numToWord($number - 1000) : '');
    } elseif ($number < 1000000) {
        return numToWord(floor($number / 1000)) . " ribu " . ($number % 1000 ? numToWord($number % 1000) : '');
    } elseif ($number < 1000000000) {
        return numToWord(floor($number / 1000000)) . " juta " . ($number % 1000000 ? numToWord($number % 1000000) : '');
    } else {
        return numToWord(floor($number / 1000000000)) . " miliar " . ($number % 1000000000 ? numToWord($number % 1000000000) : '');
    }
}

function changeDayToTwoLetter(string $dayName): string
{
    $dayName = strtolower($dayName);
    $dayName = str_replace('senin', 'SN', $dayName);
    $dayName = str_replace('selasa', 'SL', $dayName);
    $dayName = str_replace('rabu', 'RB', $dayName);
    $dayName = str_replace('kamis', 'KM', $dayName);
    $dayName = str_replace('jumat', 'JM', $dayName);
    $dayName = str_replace('sabtu', 'SB', $dayName);
    $dayName = str_replace('minggu', 'MG', $dayName);

    return $dayName;
}

?>
