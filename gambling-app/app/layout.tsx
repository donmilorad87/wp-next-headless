import type { Metadata } from "next";
import localFont from "next/font/local";
import React, { ReactNode } from "react";

import "./globals.css";

import { Toaster } from "@/components/ui/toaster";
import ThemeProvider from "@/context/Theme";

const inter = localFont({
  src: "./fonts/InterVF.ttf",
  variable: "--font-inter",
  weight: "100 200 300 400 500 600 700 800 900",
});

const spaceGrotesk = localFont({
  src: "./fonts/SpaceGroteskVF.ttf",
  variable: "--font-space-grotesk",
  weight: "100 200 300 400 500 600 700 800 900",
});

export const metadata: Metadata = {
  title: "Gambling.com Group",
  description: "Gambling NEXT.js test",
  icons: {
    icon: "/images/gambling_com_group_logo.jpeg",
  },
};

const RootLayout = async ({ children }: { children: ReactNode }) => {
  return (
    <html lang="en" suppressHydrationWarning>
      <head>
      </head>

      <body className={`${inter.className} ${spaceGrotesk.variable} antialiased`}>
        <ThemeProvider attribute="class" defaultTheme="system" enableSystem disableTransitionOnChange>
          {children}
        </ThemeProvider>
        <Toaster />
      </body>
    </html>
  );
};

export default RootLayout;
