<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
class Questions extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Questions::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        $country = DB::table('bot_country')->get();
        $country_select_list = [];
        foreach ($country as $item){
            $country_select_list[$item->id]=$item->name;
        }

        return [
            ID::make(__('ID'), 'id')->sortable(),
            Text::make(__('Title'), 'title'),
            Textarea::make(__('Answer'), 'answer'),
            Select::make('Country', 'country')->options($country_select_list)->displayUsingLabels(),
            Select::make('Parent', 'parent')->options(
                [
                    'question_0'=>"Правила в’їзду до країни",
                    'question_1'=>"Міжнародний захист",
                    'question_2'=>"Працевлаштування",
                    'question_3'=>"Допомога",
                    'question_4'=>"Інтеграція",
                    'question_5'=>"Корисні контакти",
                ]
            )->displayUsingLabels(),

        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
