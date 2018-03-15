module Main exposing (..)

import String
import Dict
import Html exposing (..)
import Html.App as App
import Html.Attributes exposing (href, class, style)
import Material
import Material.Scheme
import Material.Button as Button
import Material.Textfield as Textfield
import Material.Options exposing (css, nop)
import Question as Question


type alias Quiz =
    { name : String
    , id : Int
    , pages : List Page
    , score : Maybe ( Int, Int )
    , mdl : Material.Model
    }


type alias Page =
    { name : String
    , viewing : PageViewState
    , data : PageDataState (Dict.Dict Int Question.Question)
    }


type PageViewState
    = Selectable
    | Selected
    | Disabled


type PageDataState data
    = Inactive
    | Loading
    | Loaded data
    | Saving data
    | Saved data
    | Scoring data
    | Scored data


pageview mdl idx page =
    case page.viewing of
        Selectable ->
            div [] [ a [] [ text page.name ] ]

        Selected ->
            div [] [ a [] [ text page.name ], pagedata mdl idx page ]

        Disabled ->
            div [] [ text page.name ]


pagedata mdl idx page =
    case page.data of
        Inactive ->
            div [] []

        Loading ->
            text "Loading"

        Loaded data ->
            Dict.map (Question.render Qst Mdl mdl) data |> Dict.values |> div []

        Saving data ->
            div [] []

        Saved data ->
            div [] []

        Scoring data ->
            text "Scoring"

        Scored data ->
            text "Scored"


pagequpdate qmsg page =
    case page.data of
        Inactive ->
            page

        Loading ->
            page

        Loaded data ->
            { page | data = Loaded (Question.update qmsg data) }

        Saving data ->
            page

        Saved data ->
            page

        Scoring data ->
            page

        Scored data ->
            page


quiz : Quiz
quiz =
    { name = "Test"
    , id = 0
    , pages =
        [ { name = "Page 1"
          , viewing = Selected
          , data =
                Loaded <|
                    Dict.fromList
                        [ ( 0
                          , Question.FR
                                { label = "Hello"
                                , value = "help"
                                , answer = Just "hello"
                                }
                          )
                        ]
          }
        , { name = "Page 2"
          , viewing = Selectable
          , data =
                Loaded <|
                    Dict.fromList
                        [ ( 1
                          , Question.FR
                                { label = "Hello"
                                , value = "hello"
                                , answer = Just "help"
                                }
                          )
                        ]
          }
        ]
    , score = Nothing
    , mdl = Material.model
    }


type Msg
    = NoOp
    | Mdl (Material.Msg Msg)
    | Qst Question.Msg


update : Msg -> Quiz -> ( Quiz, Cmd Msg )
update msg quiz =
    case msg of
        NoOp ->
            ( quiz, Cmd.none )

        Mdl msg_ ->
            Material.update msg_ quiz

        Qst qmsg ->
            ( { quiz | pages = List.map (pagequpdate qmsg) quiz.pages }
            , Cmd.none
            )


view : Quiz -> Html Msg
view quiz =
    div
        [ style [ ( "padding", "2rem" ), ( "font-family", "Linux Libertine" ) ] ]
        (List.concat
            [ List.indexedMap (pageview quiz.mdl) quiz.pages
            , [ Button.render Mdl
                    [ 1000 ]
                    quiz.mdl
                    [ Button.onClick NoOp
                    , Button.ripple
                    ]
                    [ text <| "Click me" ]
              ]
            ]
        )
        |> Material.Scheme.top



-- Load Google Mdl CSS. You'll likely want to do that not in code as we
-- do here, but rather in your master .html file. See the documentation
-- for the `Material` module for details.


main : Program Never
main =
    App.program
        { init = ( quiz, Cmd.none )
        , view = view
        , subscriptions = always Sub.none
        , update = update
        }
