<?php

namespace App\DataTables;

use App\Models\EventCategory;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class EventCategoryDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->addColumn('action', function($query){
            $editBtn = "<a href='".route('admin.eventcategory.edit', $query->id)."' class='btn btn-primary'><i class='far fa-edit'></i></a>";
            $deleteBtn = "<a href='".route('admin.eventcategory.destroy', $query->id)."' class='btn btn-danger ml-2 delete-item'><i class='far fa-trash-alt'></i></a>";

            return $editBtn.$deleteBtn;
        })
        ->addColumn('status', function($query){
            if($query->status == 1){
                $button = '<label class="custom-switch mt-2">
                    <input type="checkbox" checked name="custom-switch-checkbox" data-id="'.$query->id.'" class="custom-switch-input change-status" >
                    <span class="custom-switch-indicator"></span>
                </label>';
            }else {
                $button = '<label class="custom-switch mt-2">
                    <input type="checkbox" name="custom-switch-checkbox" data-id="'.$query->id.'" class="custom-switch-input change-status">
                    <span class="custom-switch-indicator"></span>
                </label>';
            }
            return $button;
        })
        ->addColumn('admin', function($query){
            if($query->admin == 1){
                $button = '<label class="custom-switch mt-2">
                    <input type="checkbox" checked name="custom-switch-checkbox" data-id="'.$query->id.'" class="custom-switch-input change-admin" >
                    <span class="custom-switch-indicator"></span>
                </label>';
            }else {
                $button = '<label class="custom-switch mt-2">
                    <input type="checkbox" name="custom-switch-checkbox" data-id="'.$query->id.'" class="custom-switch-input change-admin">
                    <span class="custom-switch-indicator"></span>
                </label>';
            }
            return $button;
        })
        ->rawColumns(['action', 'status', 'admin'])
        ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(EventCategory $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('eventcategory-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    //->dom('Bfrtip')
                    ->orderBy(0)
                    ->selectStyleSingle()
                    ->buttons([
                        Button::make('excel'),
                        Button::make('csv'),
                        Button::make('pdf'),
                        Button::make('print'),
                        Button::make('reset'),
                        Button::make('reload')
                    ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
          Column::make('id')->width(100),
          Column::make('name'),
          Column::make('status')->width(200),
          Column::make('admin')->width(200),
          Column::computed('action')
          ->exportable(false)
          ->printable(false)
          ->width(200)
          ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'EventCategory_' . date('YmdHis');
    }
}
