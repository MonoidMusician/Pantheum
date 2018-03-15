module Main exposing (..)


type Matchable
    = Single String
    | Many (List Matchable)
    | Dynamic (String -> Bool)


match : Matchable -> String -> Bool
match pattern subject =
    case pattern of
        Single str ->
            str == subject

        Many matchables ->
            List.any (\m -> match m subject) matchables

        Dynamic fn ->
            fn subject
