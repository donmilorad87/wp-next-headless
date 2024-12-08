import { cookies } from "next/headers";
import { NextRequest, NextResponse } from "next/server";

import { decrypt } from "./lib/session";

const protectedRoutes = ["/community", "/salary", '/propose'];
const publicRoutes = ["/sign-in", "/"];

export default async function middleware(req: NextRequest) {
  const path = req.nextUrl.pathname;
  const isProtectedRoute = protectedRoutes.includes(path);
  const isPublicRoute = publicRoutes.includes(path);
  const cookieStore = await cookies();
  const cookie = cookieStore.get("session")?.value;
  console.log(cookie);

  const session = await decrypt(cookie);
  console.log(session ? "session found" : "no session found", isProtectedRoute && !session);

  if (isProtectedRoute && !session) {
    return NextResponse.redirect(new URL("/sign-in", req.nextUrl));
  }

  if (isPublicRoute && session?.token && path !== "/") {
    return NextResponse.redirect(new URL("/community", req.nextUrl));
  }

  return NextResponse.next();
}
