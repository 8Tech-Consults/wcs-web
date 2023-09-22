<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AfterDateInDatabase implements Rule
{
    protected $table;
    protected $column;
    protected $row;
    protected $attribute;

    public function __construct($table, $row, $column)
    {
        $this->table = $table;
        $this->column = $column;
        $this->row = $row;
    }

    public function passes($attribute, $value)
    {
        $this->attribute = $attribute;
        $existingDate = DB::table($this->table)
            ->where('id', $this->row)
            ->where($this->column, '<=', $value)
            ->count();

            error_log("Validation: $this->row ".$existingDate);

        return $existingDate > 0;
    }

    public function message()
    {
        return Str::replace('_',' ',$this->attribute)." must be after or equal to the ".Str::replace('_',' ',$this->column);
    }
}
