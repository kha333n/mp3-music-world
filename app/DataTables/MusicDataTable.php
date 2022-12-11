<?php

namespace App\DataTables;

use App\Models\Music;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class MusicDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     * @return \Yajra\DataTables\EloquentDataTable
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function ($music) {
                return view('dashboard.playButton', compact('music'));
            })
            ->addColumn('artist', function ($music) {
                return $music->artist->name;
            })
            ->rawColumns(['action'])
            ->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Music $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Music $model): QueryBuilder
    {
        return $model->newQuery()->with('artist');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('music-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->searching(true)
            ->deferRender(true)
            ->stateSave(true)
            ->serverSide(true)
            ->pagingType('full_numbers')
            ->fixedHeaderHeader(true)
            ->responsive(true)
            ->autoWidth(true)
            ->select(true)->parameters(
                [
                    'select.className' => 'alert alert-success',
                    'select.blurable' => true,
                ]
            )
            ->orderBy(0, 'asc');

    }

    /**
     * Get the dataTable columns definition.
     *
     * @return array
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex', 'id')
                ->title('Sr#.'),
            Column::make('name')
                ->title('Song Name'),
            Column::computed('artist')
                ->title('Artist'),
            Column::computed('action'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Music_' . date('YmdHis');
    }
}
