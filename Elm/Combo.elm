module Combo exposing (Permutator(..), phrase, repr, generate, count, example)

import List.Extra as List_
import String as String
import String.Extra as String_
import Html exposing (text, span)
import Html.Attributes exposing (style)


type Permutator
    = Combo (List Permutator)
    | Unordered (List Permutator)
    | Many (List Permutator)
    | One String


phrase : List Permutator -> Permutator
phrase words =
    Combo <| List.intersperse (One " ") words


repr : Permutator -> String
repr permutator =
    case permutator of
        Combo parts ->
            parts |> List.map repr_ |> String.join ""

        Unordered parts ->
            parts |> List.map (repr__ "{" "}") |> String.join ""

        Many parts ->
            parts |> List.map repr |> String.join "|"

        One str ->
            str


repr_ : Permutator -> String
repr_ permutator =
    case permutator of
        Many _ ->
            "(" ++ repr permutator ++ ")"

        One _ ->
            repr permutator

        Combo _ ->
            repr permutator

        Unordered _ ->
            repr permutator


repr__ : String -> String -> Permutator -> String
repr__ before after permutator =
    before ++ repr permutator ++ after


cartprod : List (List String) -> List (List String)
cartprod sequences =
    let
        aggregator sequence accumulator =
            List_.lift2 (\x y -> x :: y) sequence accumulator
    in
        List.foldr aggregator [ [] ] sequences


notempty s =
    s /= ""


generate : Permutator -> List String
generate permutator =
    case permutator of
        One str ->
            [ str ]

        Many list ->
            list |> List.map generate |> List.concat

        Combo list ->
            list |> List.map generate |> cartprod |> List.map (String.join "")

        Unordered list ->
            list |> List.map generate |> cartprod |> List.map (List.filter notempty >> List_.permutations) |> List.concat |> List.map (String.join "")


factorial : Int -> Int
factorial n =
    if n == 0 then
        1
    else
        n * factorial (n - 1)


count : Permutator -> Int
count permutator =
    case permutator of
        One _ ->
            1

        Many list ->
            list |> List.map count |> List.foldl (+) 0

        Combo list ->
            list |> List.map count |> List.foldl (*) 1

        Unordered list ->
            let
                length_ =
                    List.length list |> factorial

                produc =
                    list |> List.map count |> List.foldl (*) 1

                correction =
                    produc * length_ - (permutator |> generate |> List.length)

                -- TODO
            in
                produc * length_ - correction


ofs : List String -> List Permutator
ofs list =
    list |> List.map One


example =
    phrase
        [ Many <|
            ofs
                [ "Hello"
                , "Hi"
                ]
        , Unordered
            [ Many <| ofs [ "", "World", "Planet" ]
            , One "Outside"
            ]
        ]


main =
    (example |> repr) :: [ " (", example |> count |> toString, ")\n", example |> generate |> toString ] |> List.map text |> span [ style [ ( "white-space", "pre" ) ] ]
