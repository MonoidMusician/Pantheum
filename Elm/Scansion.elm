module Main exposing (..)

import Html exposing (Html, Attribute, beginnerProgram, text, span, div, input, button, br)
import Html.Attributes exposing (..)
import Html.Events exposing (onInput, onFocus, onClick, onBlur, on, onWithOptions)
import Json.Decode as Json
import String
import Array as Array exposing (Array)
import Regex
import Css exposing (fontFamilies, fontSize, color, px, pt, em, ex, qt, pct)
import Css.Colors


mapLeft f ( a, b ) =
    ( f a, b )


content =
    """
    Arma virumque canō, Trōiae quī prīmus ab ōrīs
    Ītaliam fātō profugus Lāvīnjaque vēnit
    lītora, multum ille et terrīs iactātus et altō
    vī superum, saevae memorem Iūnōnis ob īram,
    multa quoque et bellō passus, dum conderet urbem
    īnferretque deōs Latiō; genus unde Latīnum
    Albānīque patrēs atque altae moenia Rōmae.
    Mūsa, mihī causās memorā, quō nūmine laesō
    quidve dolēns rēgīna deum tot volvere cāsūs
    īnsignem pietāte virum, tot adīre labōrēs
    impulerit. Tantaene animīs caelestibus īrae?
    """



{-
   ¶
       Urbs antīqua fuit (Tyriī tenuēre colōnī)
       Karthāgō, Ītaliam contrā Tiberīnaque longē
       ōstia, dīves opum studiīsque asperrima bellī,
       quam Iūnō fertur terrīs magis omnibus ūnam
       posthabitā coluisse Samō. hīc illius arma,
       hīc currus fuit; hoc rēgnum dea gentibus esse,
       sī quā Fāta sinant, iam tum tenditque fovetque.
       Prōgeniem sed enim Trōiānō ā sanguine dūcī
       audierat Tyriās ōlim quae verteret arcēs;
       hinc populum lātē rēgem bellōque superbum
       ventūrum excidiō Libyae; sīc volvere Parcās.
       Id metuēns veterisque memor Sāturnia bellī,
       prīma quod ad Trōiam prō cārīs gesserat Argīs—
       necdum etiam causae īrārum saevīque dolōrēs
       exciderant animō; manet altā mente repostum
       iūdicium Paridis sprētaeque iniūria fōrmae
       et genus invīsum et raptī Ganymēdis honōrēs:
       hīs accēnsa super iactātōs aequore tōtō
       Trōas, rēliquiās Danaüm atque immītis Achillī,
       arcēbat longē Latiō, multōsque per annōs
       errābant āctī Fātīs maria omnia circum.
       Tantae mōlis erat Rōmānam condere gentem.
   ¶
       Vix ē cōnspectū Siculae tellūris in altum
       vēla dabant laetī et spūmās salis aere ruēbant,
       cum Iūnō aeternum servāns sub pectore vulnus
       haec sēcum: ‘Mēne inceptō dēsistere victam
       nec posse Ītaliā Teucrōrum āvertere rēgem!
       Quippe vetor Fātīs. Pallasne exūrere classem
       Argīvum atque ipsōs potuit summergere pontō
       ūnius ob noxam et furiās Āiācis Oīlej?
       Ipsa Iovis rapidum iaculāta ē nūbibus ignem
       disiēcitque ratēs ēvertitque aequora ventīs,
       illum exspīrantem trānsfīxō pectore flammās
       turbine corripuit scopulōque īnfīxit acūtō;
       ast ego, quae dīvum incēdō rēgīna Iovisque
       et soror et coniūnx, ūnā cum gente tot annōs
       bella gerō. Et quisquam nūmen Iūnōnis adōrat
       praetereā aut supplex ārīs impōnet honōrem?’
   ¶
       Tālia flammātō sēcum dea corde volūtāns
       nimbōrum in patriam, loca fēta furentibus Austrīs,
       Aeoliam venit. Hīc vāstō rēx Aeolus antrō
       luctantēs ventōs tempestātēsque sonōrās
       imperiō premit ac vinclīs et carcere frēnat.
       Illī indignantēs magnō cum murmure montis
       circum claustra fremunt; celsā sedet Aeolus arce
       scēptra tenēns mollitque animōs et temperat īrās.
       nī faciat, maria ac terrās caelumque profundum
       quippe ferant rapidī sēcum verrantque per aurās;
       Sed pater omnipotēns spēluncīs abdidit ātrīs
       hoc metuēns mōlemque et montēs īnsuper altōs
       imposuit, rēgemque dedit quī foedere certō
       et premere et laxās scīret dare iussus habēnās.
       Ad quem tum Iūnō supplex hīs vōcibus ūsa est:
   ¶…¶
       Dēfessī Aeneadae quae proxima lītora cursū
       contendunt petere, et Libyae vertuntur ad ōrās.
       Est in sēcessū longō locus: īnsula portum
       efficit obiectū laterum, quibus omnis ab altō
       frangitur inque sinūs scindit sēsē unda reductōs.
       Hinc atque hinc vāstae rūpēs geminīque minantur
       in caelum scopulī, quōrum sub vertice lātē
       aequora tūta silent; tum silvīs scaena coruscīs
       dēsuper, horrentīque ātrum nemus imminet umbrā.
       Fronte sub adversā scopulīs pendentibus antrum;
       intus aquae dulcēs vīvōque sedīlia saxō,
       nymphārum domus. Hīc fessās nōn vincula nāvēs
       ūlla tenent, uncō nōn alligat ancora morsū.
       Hūc septem Aenēās collēctīs nāvibus omnī
       ex numerō subit, ac magnō tellūris amōre
       ēgressī optātā potiuntur Trōës harēnā
       et sale tābentēs artūs in lītore pōnunt.
       Ac prīmum silicī scintillam excūdit Achātēs
       succēpitque ignem foliīs atque ārida circum
       nūtrīmenta dedit rapuitque in fōmite flammam.
       Tum Cererem corruptam undīs Cereāliaque arma
       expediunt fessī rērum, frūgēsque receptās
       et torrēre parant flammīs et frangere saxō.
   ¶
       Aenēās scopulum intereā cōnscendit, et omnem
       prōspectum lātē pelagō petit, Anthea sī quem
       iactātum ventō videat Phrygiāsque birēmēs
       aut Capyn aut celsīs in puppibus arma Caīcī.
       Nāvem in cōnspectū nūllam, trēs lītore cervōs
       prōspicit errantēs; hōs tōta armenta sequuntur
       ā tergō et longum per vallēs pāscitur agmen.
       Cōnstitit hīc arcumque manū celerēsque sagittās
       corripuit fīdus quae tēla gerēbat Achātēs,
       ductōrēsque ipsōs prīmum capita alta ferentēs
       cornibus arboreīs sternit, tum vulgus et omnem
       miscet agēns tēlīs nemora inter frondea turbam;
       nec prius absistit quam septem ingentia victor
       corpora fundat humī et numerum cum nāvibus aequet;
       Hinc portum petit et sociōs partītur in omnēs.
       Vīna bonus quae dejnde cadīs onerārat Acestēs
       lītore Trīnacriō dederatque abeuntibus hērōs
       dīvidit, et dictīs maerentia pectora mulcet:
   ¶
       ‘Ō sociī (neque enim ignārī sumus ante malōrum),
       ō passī graviōra, dabit deus hīs quoque fīnem.
       Vōs et Scyllaeam rabiem penitusque sonantēs
       accestis scopulōs, vōs et Cȳclōpia saxa
       expertī: revocāte animōs maestumque timōrem
       mittite; forsan et haec ōlim meminisse iuvābit.
       Per variōs cāsūs, per tot discrīmina rērum
       tendimus in Latium, sēdēs ubi Fāta quiētās
       ostendunt; illīc fās rēgna resurgere Trōiae.
       Dūrāte, et vōsmet rēbus servāte secundīs.’
       """
-}


styles =
    Css.asPairs >> Html.Attributes.style


hyphenate =
    False


type Model
    = SeekingInput String
    | Parsing (List Line)


model : Model
model =
    SeekingInput content


main =
    beginnerProgram { model = model, view = view, update = update }


type alias Line =
    List Res


type Res
    = Punct String
    | Verb Word


type alias Word =
    { syllables : List Syllable
    , gloss : String
    }


type alias Syllable =
    ( String, SyllableType )


type SyllableType
    = Long
    | Ambiguous (Maybe Bool)
    | Short
    | Elided


processtext f g s =
    Regex.find Regex.All r_word s
        |> List.concatMap
            (\match ->
                case match.submatches of
                    [ Just word, Just punct ] ->
                        [ f word, g punct ]

                    [ Just word, Nothing ] ->
                        [ f word ]

                    [ Nothing, Just punct ] ->
                        [ g punct ]

                    _ ->
                        []
            )


type Msg
    = Next


update msg model =
    case msg of
        Next ->
            case model of
                SeekingInput content ->
                    content |> lines |> List.map rescan |> Parsing

                _ ->
                    model


space =
    text " "


spaced =
    List.intersperse space


indiv =
    div []


statemap f i =
    mapLeft List.reverse
        << List.foldl
            (\a ( r, s ) ->
                let
                    ( b, z ) =
                        f a s
                in
                    ( b :: r, z )
            )
            i


inFont =
    styles
        [ fontFamilies
            [ qt "Libertinus Serif"
            , qt "Libertine Serif"
            , qt "Linux Libertine"
            ]
        , fontSize (pt 20)
        ]


view model =
    case model of
        SeekingInput content ->
            Html.div
                [ styles
                    [ Css.width (pct 95)
                    , Css.height (pct 95)
                    , Css.margin Css.auto
                    ]
                ]
                [ Html.textarea
                    [ value content
                    , inFont
                    , styles
                        [ Css.width (pct 100)
                        , Css.height (pct 95)
                        , Css.border Css.zero
                        ]
                    ]
                    []
                , Html.button
                    [ onClick Next
                    , inFont
                    , styles
                        [ Css.width (pct 100)
                        , Css.minWidth (ex 2)
                        , Css.height (pct 5)
                        , Css.border Css.zero
                        , Css.backgroundColor (Css.hex "#FF80AB")
                        ]
                    ]
                    [ text "Next" ]
                ]

        Parsing linez ->
            linez
                |> List.map viewline
                |> Html.section
                    [ inFont ]


viewline : Line -> Html Msg
viewline line =
    line |> statemap viewres ( [], 0.0 ) |> Tuple.first |> div []


viewres : Res -> Float -> ( Html Msg, Float )
viewres res index =
    case res of
        Punct s ->
            if s == "\n" then
                ( br [] [], index )
            else
                ( text s, index )

        Verb w ->
            viewword w index


viewword : Word -> Float -> ( Html Msg, Float )
viewword word index =
    viewsyllables word.syllables index |> mapLeft (span [ title word.gloss ])


viewsyllables : List Syllable -> Float -> ( List (Html Msg), Float )
viewsyllables syllables index =
    syllables |> statemap viewsyllable ( [], index ) |> mapLeft (List.intersperse hyphen)


viewsyllable ( content, stype ) index =
    let
        incr =
            case stype of
                Elided ->
                    0

                Long ->
                    1

                Ambiguous (Just True) ->
                    1

                _ ->
                    0.5

        clr =
            styles
                [ Css.color
                    << Css.hex
                  <|
                    case index of
                        0 ->
                            "#F50057"

                        2 ->
                            "#D500F9"

                        4 ->
                            "#651FFF"

                        6 ->
                            "#00E5FF"

                        8 ->
                            "#1DE9B6"

                        10 ->
                            "#00E676"

                        11 ->
                            "#2E7D32"

                        10.5 ->
                            "#FF3D00"

                        11.5 ->
                            "#FF3D00"

                        12 ->
                            "#FF3D00"

                        _ ->
                            "#A1887F"
                ]

        preview =
            div [ styles [ Css.display Css.inlineBlock ] ]
                [ div [ y_mark, clr, no_select ] [ text <| mark stype ]
                , span
                    [ styles
                        (if stype == Elided then
                            [ Css.fontStyle Css.italic ]
                         else
                            []
                        )
                    ]
                    [ text content ]
                ]
    in
        ( preview, index + incr )


lines : String -> List Line
lines content =
    String.trim content |> String.lines |> List.map (String.trim >> words)


words : String -> List Res
words content =
    processtext (\s -> Verb <| { syllables = syllables s, gloss = s }) Punct content


v =
    "(?:(?:a[eu]|oe)(?![̄̈])|[aeiouyāēīōūȳ]̄?|[aeiouy]̄|[äëïöüÿ])"


c =
    "(?:[qg]u(?=" ++ v ++ ")|[ck]h|[bcdfghjklmnprstvwxz]|\\bi(?=" ++ v ++ "))"


r_word =
    Regex.regex ("((?:" ++ c ++ "|" ++ v ++ ")+)?(.+?)?") |> Regex.caseInsensitive


r_syllable =
    Regex.regex (c ++ "*" ++ v ++ "(" ++ c ++ "*$|(?![dbpckgt][rl])" ++ c ++ "((?=" ++ c ++ "+" ++ v ++ "?))" ++ ")?") |> Regex.caseInsensitive


r_short =
    Regex.regex ("(\\bi?|[^aeiouy]|[qg]u)[aeiouy]$") |> Regex.caseInsensitive


r_long =
    Regex.regex ("[aeiouy]̄|[āēīōūȳ]|a[eu]|oe|(x|z|" ++ c ++ c ++ ")$") |> Regex.caseInsensitive


r_cx =
    Regex.regex ("^((?!h)" ++ c ++ "|[ij]" ++ v ++ "|[aeiouy]̄|[äëïöüÿ])") |> Regex.caseInsensitive


r_elision =
    Regex.regex (v ++ "m?$")


no_select =
    style
        [ ( "user-select", "none" )
        , ( "-moz-user-select", "none" )
        , ( "-moz-user-select", "-moz-none" )
        , ( "-webkit-user-select", "none" )
        ]


hyphen =
    if hyphenate then
        span [ no_select, styles [ color Css.Colors.gray ] ] [ text "-" ]
    else
        text ""


y_mark =
    styles
        [ color (Css.hex "#F50057")
        , Css.overflow Css.visible
        , Css.height (ex 1)
        , Css.transform (Css.scale2 2.0 1.4)
        , Css.margin (Css.auto)
        , Css.textAlign (Css.center)
        ]


scan1 s =
    if Regex.contains r_short s then
        Short
    else if Regex.contains r_long s then
        Long
    else
        Ambiguous Nothing


mark s =
    case s of
        Short ->
            "˘"

        Long ->
            "¯"

        Elided ->
            "˙"

        Ambiguous Nothing ->
            ""

        Ambiguous (Just True) ->
            "˜"

        Ambiguous (Just False) ->
            "ˇ"


syllable s =
    ( s, scan1 s )


weight syllable =
    if String.startsWith "x" syllable || String.startsWith "z" syllable then
        2
    else if Regex.contains r_cx syllable then
        1
    else
        0


rescan : Line -> Line
rescan line =
    let
        inner syllables bias =
            let
                start =
                    ( [], bias )

                next ( syllable, stype ) ( res, bias ) =
                    ( (if bias == 0 && Regex.contains r_elision syllable && List.isEmpty res then
                        ( syllable, Elided )
                       else if bias == 2 then
                        ( syllable, Long )
                       else
                        case stype of
                            Ambiguous Nothing ->
                                ( syllable, Ambiguous (Just (bias > 0)) )

                            _ ->
                                ( syllable, stype )
                      )
                        :: res
                    , weight syllable
                    )
            in
                List.foldr next start syllables

        update bias w =
            let
                ( syllables, b ) =
                    inner w.syllables bias
            in
                ( { w | syllables = syllables }, b )

        updaterest rest bias =
            let
                start =
                    ( [], bias )

                next res ( rest, bias ) =
                    let
                        ( r, b ) =
                            updateres bias res
                    in
                        ( r :: rest, b )
            in
                List.foldr next start rest

        updateres bias r =
            case r of
                Punct _ ->
                    ( r, bias )

                Verb w ->
                    let
                        ( new, b ) =
                            update bias w
                    in
                        ( Verb new, b )
    in
        updaterest line 2 |> Tuple.first


syllables : String -> List Syllable
syllables content =
    content |> Regex.find Regex.All r_syllable |> List.map (.match >> syllable)
