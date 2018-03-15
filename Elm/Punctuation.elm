module Punctuation exposing (Punct, concat)

import String as String
import Html exposing (text, span)
import Html.Attributes exposing (style)


type alias Punct =
    ( Bool, String, Bool )


type Part
    = Pct Punct
    | Str String


make : String -> Punct
make s =
    let
        l =
            String.trimLeft s

        r =
            String.trimRight s
    in
        ( l /= s, String.trim s, s /= r )


ws : Bool -> String
ws wh =
    if wh then
        " "
    else
        ""


fold : Part -> ( Bool, String ) -> ( Bool, String )
fold p ( wsallowed, result ) =
    case p of
        Pct ( before, text, after ) ->
            ( after, result ++ ws (wsallowed && before) ++ text )

        Str raw ->
            let
                text =
                    String.trim raw
            in
                if text == "" then
                    ( wsallowed, result )
                else
                    ( True, result ++ ws wsallowed ++ text )


concat : List Part -> String
concat ps =
    Tuple.second
        (List.foldl
            fold
            ( False, "" )
            ps
        )


period =
    make ". "


comma =
    make ", "


colon =
    make ": "


apos =
    make "'"


lparen =
    make " ("


rparen =
    make ") "


lquote =
    make " “"


rquote =
    make "” "


dash =
    make "—"


mdash =
    dash


ndash =
    make " – "


quest =
    make "? "


excl =
    make "! "


allpunctuation =
    [ period
    , comma
    , colon
    , apos
    , lparen
    , rparen
    , lquote
    , rquote
    , dash
    , mdash
    , ndash
    , quest
    , excl
    ]


main =
    (concat [ Str "  ", Pct lparen, Str " Hello\n ", Pct comma, Str "\tWorld ", Pct excl, Pct rparen ] |> text) :: [] |> span [ style [ ( "white-space", "pre" ) ] ]
