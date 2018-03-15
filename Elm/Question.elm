module Question exposing (Question(..), Msg(..), render, validate, update)

import Dict as Dict
import Html exposing (..)
import Material
import Material.Button as Button
import Material.Textfield as Textfield
import Material.Options exposing (css, nop)


type Question
    = FR FRRecord



--  | MC MCRecord
--  | CB CBRecord


type alias FRRecord =
    { label : String
    , value : String
    , answer : Maybe String
    }



{--
type alias MCRecord =
  { label : String
  , value : String
  , options : List String
  , answer : Maybe String
  }
type alias CBRecord =
  { label : String
  , value : Bool
  , answer : Maybe Bool
  }
--}


validate : Question -> Maybe Bool
validate question =
    case question of
        FR { answer, value } ->
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


type Msg
    = Setvalue Int String


setValue value question =
    case question of
        FR data ->
            FR { data | value = value }


setValues idx value questions =
    Dict.map
        (\i q ->
            if i == idx then
                setValue value q
            else
                q
        )
        questions



-- update : Msg -> Question -> Question


update msg questions =
    case msg of
        Setvalue idx value ->
            setValues idx value questions



-- render : a -> Material.Model -> Int -> Question -> Html Msg


render msger mdl2 mdl id question =
    case question of
        FR data ->
            Textfield.render mdl2
                [ id ]
                mdl
                [ Textfield.onInput (msger << Setvalue id)
                , Textfield.label data.label
                , Textfield.floatingLabel
                , Textfield.value data.value
                , if Maybe.withDefault True (validate question) then
                    nop
                  else
                    Textfield.error "Incorrect answer"
                , css "font-family" "Linux Biolinum"
                ]
