"use server";

import "server-only";
import { SignJWT, jwtVerify } from "jose";
import { cookies } from "next/headers";
import { redirect } from "next/navigation";

const secretKey = process.env.SESSION_SECRET;

const encodedKey = new TextEncoder().encode(secretKey);
interface Response {
  token: string;
  user_email: string;
  user_nicename: string;
  user_display_name: string;
}

export async function createSession(response: Response) {
  const expiresAt = new Date(Date.now() + 2 * 60 * 60 * 1000);
  const session = await encrypt({
    token: response.token,
    user_email: response.user_email,
    user_nicename: response.user_nicename,
    user_display_name: response.user_display_name,
    expiresAt,
  });

  const cookieStore = await cookies();

  cookieStore.set("bearer", response.token, {
    httpOnly: true,
    secure: true,
    expires: expiresAt,
  });

  cookieStore.set("session", session, {
    httpOnly: true,
    secure: true,
    expires: expiresAt,
  });
}

export async function deleteSession() {
  const cookieStore = await cookies();
  cookieStore.delete("session");
}

type SessionPayload = {
  token: string;
  user_email: string;
  user_nicename: string;
  user_display_name: string;
  expiresAt: Date;
};

export async function encrypt(payload: SessionPayload) {
  return new SignJWT(payload).setProtectedHeader({ alg: "HS256" }).setIssuedAt().setExpirationTime("7d").sign(encodedKey);
}

export async function decrypt(session: string | undefined = "") {
  try {
    const { payload } = await jwtVerify(session, encodedKey, {
      algorithms: ["HS256"],
    });
    return payload;
  } catch (error) {
    console.log(["Failed to verify session", error]);
  }
}
export async function logout() {
  await deleteSession();
  redirect("/sign-in");
}
