module Quiz.Page exposing (Page, ViewState(..), DataState(..), PMsg, view, update)

import Dict
import Html exposing (..)
import Material
import Quiz.Question as Question
import Parts exposing (Indexed)


type alias Page =
    { name : String
    , viewing : ViewState
    , data : DataState
    , mdl : Material.Model
    }


type ViewState
    = Selectable
    | Selected
    | Disabled


type alias Questions =
    { questions : Indexed Question.Question }


type DataState
    = Inactive
    | Loading
    | Loaded Questions
    | Saving Questions
    | Saved Questions
    | Scoring Questions
    | Scored Questions



{-
   init : String -> ViewState -> Questions -> Page
   init name viewing data =
     { name=name, viewing=viewing, data=data, mdl = Material.model }
-}
--type Msg = Qst msg


view l idx page =
    case page.viewing of
        Selectable ->
            div [] [ a [] [ text page.name ] ]

        Selected ->
            div [] [ a [] [ text page.name ], data l idx page ]

        Disabled ->
            div [] [ text page.name ]


data l idx page =
    case page.data of
        Inactive ->
            div [] []

        Loading ->
            text "Loading"

        Loaded data ->
            Dict.map (\i q -> Question.view (l << (Qst i)) q) data.questions |> Dict.values |> div []

        --text <| "Loaded " ++ (Dict.size data.questions |> toString)
        Saving data ->
            div [] []

        Saved data ->
            div [] []

        Scoring data ->
            text "Scoring"

        Scored data ->
            text "Scored"



{-
   qupdate qmsg page =
     case page.data of
       Inactive ->
         (page, Cmd.none)
       Loading ->
         (page, Cmd.none)
       Loaded data ->
         let new = Question.update qmsg data
         in ({ page | data = Loaded (fst new) }, snd new)
       Saving data ->
         (page, Cmd.none)
       Saved data ->
         (page, Cmd.none)
       Scoring data ->
         (page, Cmd.none)
       Scored data ->
         (page, Cmd.none)
-}


type PMsg
    = Mdl (Material.Msg PMsg)
    | Qst Parts.Index (Question.QMsg PMsg)


updata : Parts.Index -> Question.QMsg PMsg -> Page -> ( Page, Cmd PMsg )
updata idx msg_ page =
    case page.data of
        Inactive ->
            ( page, Cmd.none )

        Loading ->
            ( page, Cmd.none )

        Loaded data ->
            let
                item =
                    Dict.get idx data.questions

                result =
                    Maybe.map (Question.update msg_) item

                topagecmd =
                    \r ->
                        let
                            qs =
                                data.questions |> Dict.update idx (always <| Just <| Tuple.first r)
                        in
                            ( { page | data = Loaded { questions = qs } }, Cmd.map (Qst idx) (Tuple.second r) )
            in
                result
                    |> Maybe.map topagecmd
                    |> Maybe.withDefault ( page, Cmd.none )

        Saving data ->
            ( page, Cmd.none )

        Saved data ->
            ( page, Cmd.none )

        Scoring data ->
            ( page, Cmd.none )

        Scored data ->
            ( page, Cmd.none )


update msg page =
    case msg of
        Mdl msg_ ->
            Material.update msg_ page

        Qst idx msg_ ->
            updata idx msg_ page
