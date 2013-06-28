<?php
class WikiLexerConstants {

  /** End of File. */
  const EOF = 0;
  /** RegularExpression Id. */
  const NEW_LINE = 1;
  /** RegularExpression Id. */
  const SPACE = 2;
  /** RegularExpression Id. */
  const SPECIAL_SYMBOL = 3;
  /** RegularExpression Id. */
  const CHAR = 4;
  /** RegularExpression Id. */
  const PARAMS = 5;
  /** RegularExpression Id. */
  const TABLE_BEGIN = 6;
  /** RegularExpression Id. */
  const TABLE_END = 7;
  /** RegularExpression Id. */
  const TABLE_CAPTION = 8;
  /** RegularExpression Id. */
  const TABLE_ROW = 9;
  /** RegularExpression Id. */
  const TABLE_CELL = 10;
  /** RegularExpression Id. */
  const TABLE_CELL_NL = 11;
  /** RegularExpression Id. */
  const HORLINE = 12;
  /** RegularExpression Id. */
  const HEADER = 13;
  /** RegularExpression Id. */
  const LI_SYMBOL = 14;
  /** RegularExpression Id. */
  const LI = 15;
  /** RegularExpression Id. */
  const ALPHA_CHAR = 16;
  /** RegularExpression Id. */
  const NUM_CHAR = 17;
  /** RegularExpression Id. */
  const ALPHANUM_CHAR = 18;
  /** RegularExpression Id. */
  const IDENTIFIER_CHAR = 19;
  /** RegularExpression Id. */
  const IDENTIFIER = 20;
  /** RegularExpression Id. */
  const URI = 21;
  /** RegularExpression Id. */
  const ALPHA = 22;
  /** RegularExpression Id. */
  const DIGIT = 23;
  /** RegularExpression Id. */
  const HEXDIG = 24;
  /** RegularExpression Id. */
  const URI_GEN_DELIMS = 25;
  /** RegularExpression Id. */
  const URI_SUB_DELIMS = 26;
  /** RegularExpression Id. */
  const URI_UNRESERVED = 27;
  /** RegularExpression Id. */
  const URI_RESERVED = 28;
  /** RegularExpression Id. */
  const URI_SCHEME = 29;
  /** RegularExpression Id. */
  const URI_SCHEME_COMPOSITE = 30;
  /** RegularExpression Id. */
  const URI_PCT_ENCODED = 31;
  /** RegularExpression Id. */
  const URI_PCHAR_FIRST = 32;
  /** RegularExpression Id. */
  const URI_PCHAR = 33;
  /** RegularExpression Id. */
  const URI_QUERY = 34;
  /** RegularExpression Id. */
  const URI_FRAGMENT = 35;
  /** RegularExpression Id. */
  const URI_HIER_PART = 36;
  /** RegularExpression Id. */
  const URI_AUTHORITY = 37;
  /** RegularExpression Id. */
  const URI_USERINFO = 38;
  /** RegularExpression Id. */
  const URI_PATH_ABEMPTY = 39;
  /** RegularExpression Id. */
  const URI_PATH_ABSOLUTE = 40;
  /** RegularExpression Id. */
  const URI_PATH_ROOTLESS = 41;
  /** RegularExpression Id. */
  const URI_SEGMENT = 42;
  /** RegularExpression Id. */
  const URI_SEGMENT_NZ = 43;
  /** RegularExpression Id. */
  const URI_SEGMENT_NZ_NC = 44;
  /** RegularExpression Id. */
  const URI_PORT = 45;
  /** RegularExpression Id. */
  const URI_HOST = 46;
  /** RegularExpression Id. */
  const URI_REG_NAME = 47;
  /** RegularExpression Id. */
  const I_TABLE_BEGIN = 48;
  /** RegularExpression Id. */
  const I_TABLE_END = 49;
  /** RegularExpression Id. */
  const I_TABLE_CAPTION = 50;
  /** RegularExpression Id. */
  const I_TABLE_ROW = 51;
  /** RegularExpression Id. */
  const I_TABLE_CELL = 52;
  /** RegularExpression Id. */
  const I_TABLE_CELL_NL = 53;
  /** RegularExpression Id. */
  const I_LIST_ITEM = 54;
  /** RegularExpression Id. */
  const I_HEADER_BEGIN = 55;
  /** RegularExpression Id. */
  const I_HORLINE = 56;
  /** RegularExpression Id. */
  const I_HEADER_END = 57;
  /** RegularExpression Id. */
  const D_TABLE_BEGIN = 58;
  /** RegularExpression Id. */
  const D_TABLE_END = 59;
  /** RegularExpression Id. */
  const D_TABLE_CAPTION = 60;
  /** RegularExpression Id. */
  const D_TABLE_ROW = 61;
  /** RegularExpression Id. */
  const D_TABLE_CELL = 62;
  /** RegularExpression Id. */
  const D_TABLE_CELL_NL = 63;
  /** RegularExpression Id. */
  const D_LIST_ITEM = 64;
  /** RegularExpression Id. */
  const D_HEADER_BEGIN = 65;
  /** RegularExpression Id. */
  const D_HORLINE = 66;
  /** RegularExpression Id. */
  const D_HEADER_END = 67;
  /** RegularExpression Id. */
  const IMAGE_ATTR = 68;
  /** RegularExpression Id. */
  const IMAGE_CAPTION_BEGIN = 69;
  /** RegularExpression Id. */
  const IMAGE_END = 70;
  /** RegularExpression Id. */
  const IMAGE_BEGIN = 71;
  /** RegularExpression Id. */
  const TEMPLATE_BEGIN = 72;
  /** RegularExpression Id. */
  const TEMPLATE_END = 73;
  /** RegularExpression Id. */
  const SPACES = 74;
  /** RegularExpression Id. */
  const WORD = 75;
  /** RegularExpression Id. */
  const SYMBOL = 76;
  /** RegularExpression Id. */
  const NL = 77;
  /** RegularExpression Id. */
  const REF_EMPTY = 78;
  /** RegularExpression Id. */
  const REF_BEGIN = 79;
  /** RegularExpression Id. */
  const REF_END = 80;
  /** RegularExpression Id. */
  const TAG_EMPTY = 81;
  /** RegularExpression Id. */
  const TAG_BEGIN = 82;
  /** RegularExpression Id. */
  const TAG_END = 83;
  /** RegularExpression Id. */
  const BOLD = 84;
  /** RegularExpression Id. */
  const ITALIC = 85;
  /** RegularExpression Id. */
  const INT_LINK_BEGIN = 86;
  /** RegularExpression Id. */
  const EXT_LINK_BEGIN = 87;
  /** RegularExpression Id. */
  const EXT_LINK_END = 88;
  /** RegularExpression Id. */
  const INT_LINK_PREFIX = 89;
  /** RegularExpression Id. */
  const INT_LINK_SEPARATOR = 90;
  /** RegularExpression Id. */
  const INT_LINK_SPACES = 91;
  /** RegularExpression Id. */
  const INT_LINK_END = 92;
  /** RegularExpression Id. */
  const INT_LINK_CONTENT = 93;
  /** RegularExpression Id. */
  const INT_LINK_SYMBOL = 94;

  /** Literal token values. */
  /*const tokenImage = array(
    "<EOF>",
    "<NEW_LINE>",
    "<SPACE>",
    "<SPECIAL_SYMBOL>",
    "<CHAR>",
    "<PARAMS>",
    "<TABLE_BEGIN>",
    "\"|}\"",
    "<TABLE_CAPTION>",
    "<TABLE_ROW>",
    "<TABLE_CELL>",
    "<TABLE_CELL_NL>",
    "<HORLINE>",
    "<HEADER>",
    "<LI_SYMBOL>",
    "<LI>",
    "<ALPHA_CHAR>",
    "<NUM_CHAR>",
    "<ALPHANUM_CHAR>",
    "<IDENTIFIER_CHAR>",
    "<IDENTIFIER>",
    "<URI>",
    "<ALPHA>",
    "<DIGIT>",
    "<HEXDIG>",
    "<URI_GEN_DELIMS>",
    "<URI_SUB_DELIMS>",
    "<URI_UNRESERVED>",
    "<URI_RESERVED>",
    "<URI_SCHEME>",
    "<URI_SCHEME_COMPOSITE>",
    "<URI_PCT_ENCODED>",
    "<URI_PCHAR_FIRST>",
    "<URI_PCHAR>",
    "<URI_QUERY>",
    "<URI_FRAGMENT>",
    "<URI_HIER_PART>",
    "<URI_AUTHORITY>",
    "<URI_USERINFO>",
    "<URI_PATH_ABEMPTY>",
    "<URI_PATH_ABSOLUTE>",
    "<URI_PATH_ROOTLESS>",
    "<URI_SEGMENT>",
    "<URI_SEGMENT_NZ>",
    "<URI_SEGMENT_NZ_NC>",
    "<URI_PORT>",
    "<URI_HOST>",
    "<URI_REG_NAME>",
    "<I_TABLE_BEGIN>",
    "<I_TABLE_END>",
    "<I_TABLE_CAPTION>",
    "<I_TABLE_ROW>",
    "<I_TABLE_CELL>",
    "<I_TABLE_CELL_NL>",
    "<I_LIST_ITEM>",
    "<I_HEADER_BEGIN>",
    "<I_HORLINE>",
    "<I_HEADER_END>",
    "<D_TABLE_BEGIN>",
    "<D_TABLE_END>",
    "<D_TABLE_CAPTION>",
    "<D_TABLE_ROW>",
    "<D_TABLE_CELL>",
    "<D_TABLE_CELL_NL>",
    "<D_LIST_ITEM>",
    "<D_HEADER_BEGIN>",
    "<D_HORLINE>",
    "<D_HEADER_END>",
    "<IMAGE_ATTR>",
    "\"|\"",
    "\"]]\"",
    "<IMAGE_BEGIN>",
    "<TEMPLATE_BEGIN>",
    "\"}}\"",
    "<SPACES>",
    "<WORD>",
    "<SYMBOL>",
    "<NL>",
    "<REF_EMPTY>",
    "<REF_BEGIN>",
    "\"</ref>\"",
    "<TAG_EMPTY>",
    "<TAG_BEGIN>",
    "<TAG_END>",
    "\"\\\'\\\'\\\'\"",
    "\"\\\'\\\'\"",
    "\"[[\"",
    "\"[\"",
    "\"]\"",
    "<INT_LINK_PREFIX>",
    "\"|\"",
    "<INT_LINK_SPACES>",
    "<INT_LINK_END>",
    "<INT_LINK_CONTENT>",
    "<INT_LINK_SYMBOL>",
  );
  */
}
