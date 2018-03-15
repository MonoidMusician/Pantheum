module Main exposing (..)

import String as String
import Dict as Dict
import Html exposing (..)
import Html.App as App
import Html.Attributes exposing (href, class, style)
import Material
import Material.Scheme
import Material.Button as Button
import Material.Textfield as Textfield
import Material.Options exposing (css, nop)
import Question as Question


-- MODEL


type alias Model =
    { qs : Dict.Dict Int Question.Question
    , mdl : Material.Model
    , qid : Int
    }


type alias Quiz =
    { pages : List Page }


type alias Page =
    { questions : List Question.Question }


defaultQ =
    Question.Input
        { label = "Hello"
        , value = "hello"
        , answer = Just "hello"
        , correct = Nothing
        }


model : Model
model =
    { qs = Dict.fromList [ ( 0, defaultQ ) ]
    , mdl = Material.model
    , qid = 1
    }



-- ACTION, UPDATE


type Msg
    = Duplicate
    | Mdl (Material.Msg Msg)
    | Qst Question.Msg


update : Msg -> Model -> ( Model, Cmd Msg )
update msg model =
    case msg of
        Qst qmsg ->
            ( { model | qs = Question.update qmsg model.qs }
            , Cmd.none
            )

        Duplicate ->
            ( { model | qid = model.qid + 1, qs = Dict.insert model.qid (Maybe.withDefault defaultQ (Dict.get 0 model.qs)) model.qs }
            , Cmd.none
            )

        Mdl msg_ ->
            Material.update msg_ model



-- VIEW


view : Model -> Html Msg
view model =
    div
        [ style [ ( "padding", "2rem" ), ( "font-family", "Linux Libertine" ) ] ]
        (List.concat
            [ Dict.values (Dict.map (Question.render Qst Mdl model.mdl) model.qs)
            , [ Button.render Mdl
                    [ 1000 ]
                    model.mdl
                    [ Button.onClick Duplicate
                    , Button.ripple
                    ]
                    [ text <| "Click me (" ++ (model.qs |> Dict.values |> List.filter (Question.validate >> Maybe.withDefault False) |> List.length |> toString) ++ " correct)" ]
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
        { init = ( model, Cmd.none )
        , view = view
        , subscriptions = always Sub.none
        , update = update
        }
