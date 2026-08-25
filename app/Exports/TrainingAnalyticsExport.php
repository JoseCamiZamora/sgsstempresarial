<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\{FromArray,WithHeadings,WithTitle};
class TrainingAnalyticsExport implements FromArray,WithHeadings,WithTitle { public function __construct(private array$rows,private array$headings,private string$title='Capacitaciones'){} public function array():array{return$this->rows;}public function headings():array{return$this->headings;}public function title():string{return mb_substr($this->title,0,31);} }
