module Main exposing (..)

import String
import Dict
import List.Extra as List_
import Html exposing (..)
import Html.App as App
import Html.Attributes exposing (href, class, style)
import Material
import Material.Scheme
import Material.Button as Button
import Material.Textfield as Textfield
import Material.Options exposing (css, nop)
import Quiz.Question as Question
import Quiz.Page as Page


type alias Quiz =
    { name : String
    , id : Int
    , pages : List Page.Page
    , score : Maybe ( Int, Int )
    , mdl : Material.Model
    }


quiz : Quiz
quiz =
    { name = "Test"
    , id = 0
    , pages =
        [ { name = "Page 1"
          , viewing = Page.Selected
          , data =
                Page.Loaded <|
                    Question.fromList
                        [ ( [ 0 ]
                          , Question.Question "Hello"
                                (Question.FR "help" <| Just "hello")
                                Material.model
                          )
                        ]
          , mdl = Material.model
          }
        , { name = "Page 2"
          , viewing = Page.Selectable
          , data =
                Page.Loaded <|
                    Question.fromList
                        [ ( [ 1 ]
                          , Question.Question "Help"
                                (Question.FR "hello" <| Just "help")
                                Material.model
                          )
                        ]
          , mdl = Material.model
          }
        ]
    , score = Nothing
    , mdl = Material.model
    }


type MMsg
    = NoOp
    | Mdl (Material.Msg MMsg)
    | Qst Int Page.PMsg


update : MMsg -> Quiz -> ( Quiz, Cmd MMsg )
update msg quiz =
    case msg of
        NoOp ->
            ( quiz, Cmd.none )

        Mdl msg_ ->
            Material.update msg_ quiz

        Qst idx qmsg ->
            let
                item =
                    List_.getAt idx quiz.pages

                result =
                    Maybe.map (Page.update qmsg) item

                topagecmd =
                    \r ->
                        let
                            pages =
                                quiz.pages |> List_.updateAt idx (always <| Tuple.first r) |> Maybe.withDefault quiz.pages
                        in
                            ( { quiz | pages = pages }, Cmd.map (Qst idx) (Tuple.second r) )
            in
                result
                    |> Maybe.map topagecmd
                    |> Maybe.withDefault ( quiz, Cmd.none )


view : Quiz -> Html MMsg
view quiz =
    div
        [ style [ ( "padding", "2rem" ), ( "font-family", "Linux Libertine" ) ] ]
        (List.concat
            [ List.indexedMap (\i v -> Page.view (Qst i) i v) quiz.pages
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
