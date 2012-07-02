/**
 * This file is autogenerated by buildLRTables.php and contains the IDs of differnet
 * tokens.
 */

#include "scanner.h"

const char* ws_scanner_token_name(ws_token_type type) {
	switch( type ) {
		case WS_TOKEN_END:
			return "$";
		case WS_TOKEN_FUNCTION:
			return "function";
		case WS_TOKEN_ID:
			return "id";
		case WS_TOKEN_LEFTBRACKET:
			return "leftbracket";
		case WS_TOKEN_RIGHTBRACKET:
			return "rightbracket";
		case WS_TOKEN_LEFTCURLY:
			return "leftcurly";
		case WS_TOKEN_RIGHTCURLY:
			return "rightcurly";
		case WS_TOKEN_COMMA:
			return "comma";
		case WS_TOKEN_SEMICOLON:
			return "semicolon";
		case WS_TOKEN_IF:
			return "if";
		case WS_TOKEN_ELSE:
			return "else";
		case WS_TOKEN_FOR:
			return "for";
		case WS_TOKEN_IN:
			return "in";
		case WS_TOKEN_TRY:
			return "try";
		case WS_TOKEN_CATCH:
			return "catch";
		case WS_TOKEN_RETURN:
			return "return";
		case WS_TOKEN_APPEND:
			return "append";
		case WS_TOKEN_YIELD:
			return "yield";
		case WS_TOKEN_SETTO:
			return "setto";
		case WS_TOKEN_TRINARY:
			return "trinary";
		case WS_TOKEN_COLON:
			return "colon";
		case WS_TOKEN_LOGICOP:
			return "logicop";
		case WS_TOKEN_COMPAREOP:
			return "compareop";
		case WS_TOKEN_EQUALSTO:
			return "equalsto";
		case WS_TOKEN_SUM:
			return "sum";
		case WS_TOKEN_MUL:
			return "mul";
		case WS_TOKEN_POW:
			return "pow";
		case WS_TOKEN_INVERT:
			return "invert";
		case WS_TOKEN_CONTAINS:
			return "contains";
		case WS_TOKEN_BREAK:
			return "break";
		case WS_TOKEN_CONTINUE:
			return "continue";
		case WS_TOKEN_LEFTSQUARE:
			return "leftsquare";
		case WS_TOKEN_RIGHTSQUARE:
			return "rightsquare";
		case WS_TOKEN_ISSET:
			return "isset";
		case WS_TOKEN_DELETE:
			return "delete";
		case WS_TOKEN_DOUBLECOLON:
			return "doublecolon";
		case WS_TOKEN_SELF:
			return "self";
		case WS_TOKEN_STRING:
			return "string";
		case WS_TOKEN_INT:
			return "int";
		case WS_TOKEN_FLOAT:
			return "float";
		case WS_TOKEN_TRUE:
			return "true";
		case WS_TOKEN_FALSE:
			return "false";
		case WS_TOKEN_NULL:
			return "null";

		default:
			return "???";
	}
}