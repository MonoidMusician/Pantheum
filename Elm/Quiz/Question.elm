module Quiz.Question exposing (Question, QMsg(..), Value(..), fromList, view, validate, update)

import Dict
import Html exposing (..)
import Html.Events as Events
import Material
import Material.Button as Button
import Material.Textfield as Textfield
import Material.Toggles as Toggles
import Material.Options exposing (css, nop)
import Parts exposing (Indexed)


type alias Question =
    { label : String
    , value : Value
    , mdl : Material.Model
    }


type Value
    = FR String (Maybe String)
    | MC String (List String) (Maybe String)
    | CB Bool (Maybe Bool)


fromList questions =
    { questions = Dict.fromList questions }


validate : Question -> Maybe Bool
validate question =
    case question.value of
        FR value answer ->
            case answer of
                Nothing ->
                    Nothing

                Just valid ->
                    if value == "" then
                        Nothing
                    else if valid == value then
                        Just True
                    else
                        Just False

        MC value options answer ->
            case answer of
                Nothing ->
                    Nothing

                Just valid ->
                    if value == "" then
                        Nothing
                    else if valid == value then
                        Just True
                    else
                        Just False

        CB value answer ->
            Nothing


type QMsg a
    = Setvalue String
    | Mdl (Material.Msg a)


setValue value val =
    case val of
        FR _ answer ->
            FR value answer

        MC _ options answer ->
            MC value options answer

        CB _ answer ->
            CB (value == "true") answer


update : QMsg a -> Question -> ( Question, Cmd a )
update msg question =
    case msg of
        Setvalue value ->
            ( { question | value = setValue value question.value }, Cmd.none )

        Mdl msg_ ->
            Material.update msg_ question


view : (QMsg m -> m) -> Question -> Html m
view lift question =
    case question.value of
        FR value _ ->
            Textfield.render (lift << Mdl) [ 0 ] question.mdl []

        {- input [
           Events.onInput (lift << Setvalue)
           , value value
           ] []
        -}
        {- Textfield.render (lift << Mdl) [0] question.mdl
           [ Textfield.label question.label
           , Textfield.floatingLabel
           , Textfield.value value
           --, Textfield.onInput (lift << Setvalue)
           , if Maybe.withDefault True (validate question) then nop else Textfield.error "Incorrect answer"
           , css "font-family" "Linux Biolinum"
           ]
        -}
        MC value options _ ->
            div [] []

        {- Textfield.render (lift << Mdl) [0] question.mdl
           [ Textfield.label question.label
           , Textfield.floatingLabel
           , Textfield.value value
           --, Textfield.onInput (lift << Setvalue)
           , if Maybe.withDefault True (validate question) then nop else Textfield.error "Incorrect answer"
           , css "font-family" "Linux Biolinum"
           ]
        -}
        CB value _ ->
            div [] []



{- Toggles.checkbox (lift << Mdl) [0] question.mdl
   [ Toggles.value value ] [ text question.label ]
-}


type alias Container c =
    { c | questions : Indexed Question }


set : Parts.Set (Indexed Question) (Container c)
set x y =
    { y | questions = x }



{-
   render : (Parts.Msg (Container c) m -> m) -> Parts.Index -> Container c -> Html m
   render =
     Parts.create view (Parts.generalize update) .questions set (Question "hi" (CB True Nothing) (Material.model))
-}
